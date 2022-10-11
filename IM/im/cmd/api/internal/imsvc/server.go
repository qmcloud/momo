package imsvc

import (
	"backend/common/tool"
	"backend/service/im/cmd/api/internal/svc"
	"backend/service/im/cmd/rpc/pb"
	"context"
	"encoding/json"
	"fmt"
	"github.com/gorilla/websocket"
	"github.com/sirupsen/logrus"
	"net/http"
	"time"
)

type Server struct {
	Buckets   []*Bucket
	Options   ServerOptions
	bucketIdx uint32
	Ctx       context.Context
	SvcCtx    *svc.ServiceContext
}

type ServerOptions struct {
	WriteWait       time.Duration
	PongWait        time.Duration
	PingPeriod      time.Duration
	MaxMessageSize  int64
	ReadBufferSize  int
	WriteBufferSize int
	BroadcastSize   int
}

func (s Server) Start() {
	Run(s.SvcCtx)
}
func (s Server) Stop() {
	fmt.Println("im service was stop...")
}

func Run(svcCtx *svc.ServiceContext) error {
	Buckets := make([]*Bucket, svcCtx.Config.ConnectBucket.CpuNum)
	for i := 0; i < svcCtx.Config.ConnectBucket.CpuNum; i++ {
		Buckets[i] = NewBucket(BucketOptions{
			ChannelSize:   svcCtx.Config.ConnectBucket.Channel,
			RoomSize:      svcCtx.Config.ConnectBucket.Room,
			RoutineAmount: svcCtx.Config.ConnectBucket.RoutineAmount,
			RoutineSize:   svcCtx.Config.ConnectBucket.RoutineSize,
		})
	}
	DefaultServer = NewServer(Buckets, ServerOptions{
		WriteWait:       10 * time.Second,
		PongWait:        60 * time.Second,
		PingPeriod:      54 * time.Second,
		MaxMessageSize:  1024,
		ReadBufferSize:  1024,
		WriteBufferSize: 1024,
		BroadcastSize:   1024,
	})
	mux := http.NewServeMux()
	mux.HandleFunc(svcCtx.Config.WSHander, func(w http.ResponseWriter, r *http.Request) {
		new(Connect).serveWs(DefaultServer, w, r)
	})
	fmt.Printf("Starting WS at 0.0.0.0:%d%s...\n", svcCtx.Config.WSPort, svcCtx.Config.WSHander)
	err := http.ListenAndServe(fmt.Sprintf(":%d", svcCtx.Config.WSPort), mux)
	if err != nil {
		fmt.Println("ws err：", err)
		return err
	}
	return nil
}

func NewServer(b []*Bucket, options ServerOptions) *Server {
	s := new(Server)
	s.Buckets = b
	s.Options = options
	s.bucketIdx = uint32(len(b))
	return s
}

//reduce lock competition, use google city hash insert to different bucket
func (s *Server) Bucket(userId int64) *Bucket {
	userIdStr := fmt.Sprintf("%d", userId)
	idx := tool.CityHash32([]byte(userIdStr), uint32(len(userIdStr))) % s.bucketIdx
	return s.Buckets[idx]
}

func (s *Server) writePump(ch *Channel, c *Connect) {
	//PingPeriod default eq 54s
	ticker := time.NewTicker(s.Options.PingPeriod)
	defer func() {
		ticker.Stop()
		ch.conn.Close()
	}()

	for {
		select {
		case message, ok := <-ch.broadcast:
			//write data dead time , like http timeout , default 10s
			ch.conn.SetWriteDeadline(time.Now().Add(s.Options.WriteWait))
			if !ok {
				logrus.Warn("SetWriteDeadline not ok")
				ch.conn.WriteMessage(websocket.CloseMessage, []byte{})
				return
			}
			w, err := ch.conn.NextWriter(websocket.TextMessage)
			if err != nil {
				logrus.Warn(" ch.conn.NextWriter err :%s  ", err.Error())
				return
			}
			logrus.Infof("message write body:%s", message.Body)
			w.Write(message.Body)
			if err := w.Close(); err != nil {
				return
			}
		case <-ticker.C:
			//heartbeat，if ping error will exit and close current websocket conn
			ch.conn.SetWriteDeadline(time.Now().Add(s.Options.WriteWait))
			logrus.Infof("websocket.PingMessage :%v", websocket.PingMessage)
			if err := ch.conn.WriteMessage(websocket.PingMessage, nil); err != nil {
				return
			}
		}
	}
}

func (s *Server) readPump(ch *Channel, c *Connect) {
	defer func() {
		logrus.Infof("start exec disConnect ...")
		if ch.Room == nil || ch.userId == 0 {
			logrus.Infof("roomId and userId eq 0")
			ch.conn.Close()
			return
		}
		logrus.Infof("exec disConnect ...")
		disConnectRequest := new(pb.DisConnectRequest)
		disConnectRequest.RoomId = ch.Room.Id
		disConnectRequest.UserId = ch.userId
		s.Bucket(ch.userId).DeleteChannel(ch)

		if _, err := s.SvcCtx.WsRpcConf.DisConnect(s.Ctx, disConnectRequest); err != nil {
			logrus.Warnf("DisConnect err :%s", err.Error())
		}
		ch.conn.Close()
	}()

	ch.conn.SetReadLimit(s.Options.MaxMessageSize)
	ch.conn.SetReadDeadline(time.Now().Add(s.Options.PongWait))
	ch.conn.SetPongHandler(func(string) error {
		ch.conn.SetReadDeadline(time.Now().Add(s.Options.PongWait))
		return nil
	})

	for {
		_, message, err := ch.conn.ReadMessage()
		if err != nil {
			if websocket.IsUnexpectedCloseError(err, websocket.CloseGoingAway, websocket.CloseAbnormalClosure) {
				logrus.Errorf("readPump ReadMessage err:%s", err.Error())
				return
			}
		}
		if message == nil {
			return
		}
		var connReq *pb.ConnectRequest
		logrus.Infof("get a message :%s", message)
		if err := json.Unmarshal([]byte(message), &connReq); err != nil {
			logrus.Errorf("message struct %+v", connReq)
		}
		if connReq.AuthToken == "" {
			logrus.Errorf("s.operator.Connect no authToken")
			return
		}
		connReq.ServerId = c.ServerId //config.Conf.Connect.ConnectWebsocket.ServerId

		ConnReply, err := s.SvcCtx.WsRpcConf.Connect(s.Ctx, connReq)
		if err != nil {
			logrus.Errorf("s.operator.Connect error %s", err.Error())
			return
		}
		if ConnReply.UserId == 0 {
			logrus.Error("Invalid AuthToken ,userId empty")
			return
		}
		logrus.Infof("websocket rpc call return userId:%d,RoomId:%d", ConnReply.UserId, connReq.RoomId)
		b := s.Bucket(ConnReply.UserId)
		//insert into a bucket
		err = b.Put(ConnReply.UserId, connReq.RoomId, ch)
		if err != nil {
			logrus.Errorf("conn close err: %s", err.Error())
			ch.conn.Close()
		}
	}
}
