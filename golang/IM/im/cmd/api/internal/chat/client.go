package chart

import (
	"bytes"
	"encoding/json"
	"fmt"
	"net/http"
	"time"

	"github.com/gorilla/websocket"
)

type Client struct {
	hub     *Hub
	conn    *websocket.Conn
	send    chan []byte
	room    []string
	Id      uint64
	Detail  interface{}
	uid     string
	channel string
}

const (
	// Time allowed to write a message to the peer.
	writeWait = 10 * time.Second

	// Time allowed to read the next pong message from the peer.
	pongWait = 60 * time.Second

	// Send pings to peer with this period. Must be less than pongWait.
	pingPeriod = (pongWait * 9) / 10

	// Maximum message size allowed from peer.
	maxMessageSize = 1024 * 10

	// send buffer size
	bufSize = 1024 * 10
)

var (
	newline = []byte{'\n'}
	space   = []byte{' '}
)

var clients map[string]*Client

type ServerOptions struct {
	WriteWait       time.Duration
	PongWait        time.Duration
	PingPeriod      time.Duration
	MaxMessageSize  int64
	ReadBufferSize  int
	WriteBufferSize int
	BroadcastSize   int
}

var upGrader = websocket.Upgrader{
	ReadBufferSize:  1024 * 10,
	WriteBufferSize: 1024 * 10,
	CheckOrigin: func(r *http.Request) bool {
		return true
	},
}

func (c *Client) WriteMsg() {
	ticker := time.NewTicker(pingPeriod)
	defer func() {
		ticker.Stop()
		_ = c.conn.Close()
	}()
	for {
		select {
		case message, ok := <-c.send:
			if !ok {
				c.conn.WriteMessage(websocket.CloseMessage, []byte{}) // 错误 关闭 channel
				msgStore.ErrorLogServer(fmt.Errorf("系统错误：错误 关闭 channel"))
				break
			}
			w, err := c.conn.NextWriter(websocket.TextMessage)
			if err != nil {
				return
			}
			_, _ = w.Write(message)
			n := len(c.send)
			for i := 0; i < n; i++ {
				w.Write(newline)
				w.Write(<-c.send)
			}
			if err := w.Close(); err != nil {
				msgStore.ErrorLogServer(fmt.Errorf("Close：关闭 "))
				break
			}
		case <-ticker.C:
			c.conn.SetWriteDeadline(time.Now().Add(writeWait))
			if err := c.conn.WriteMessage(websocket.PingMessage, nil); err != nil {
				return
			}

		}
	}
}

func (c *Client) ReadMsg() {
	defer func() {
		c.hub.unregister <- c
		_ = c.conn.Close()
	}()
	c.conn.SetReadLimit(maxMessageSize)
	c.conn.SetReadDeadline(time.Now().Add(pongWait))
	c.conn.SetPongHandler(func(string) error { c.conn.SetReadDeadline(time.Now().Add(pongWait)); return nil })
	for {
		_, strByte, err := c.conn.ReadMessage()
		if err != nil {
			if websocket.IsUnexpectedCloseError(err, websocket.CloseGoingAway, websocket.CloseAbnormalClosure) {
				//msgStore.ErrorLogServer(fmt.Errorf("系统错误,异常退出：%v", err))
			}
			break
		}
		if clients[c.uid] != c {
			msgStore.ErrorLogServer(fmt.Errorf("用户`%d`未登录，不能发送消息", c.Id))
			continue
		}
		//过滤消息
		bytemessage := bytes.TrimSpace(bytes.Replace(strByte, newline, space, -1))

		message := Message{}
		json.Unmarshal(bytemessage, &message)

		message.SendTime = time.Now().Format("2006-01-02 15:04:05")
		message.UserId = c.Id
		message.Detail = c.Detail
		message.SingleMsg = bytemessage
		/*if message.ToUserId > 0 {
			if _, ok := clients[message.ToUserId]; !ok {
				continue
			}
		} else if _, ok := c.hub.clients[message.ChannelId][c]; !ok {
			msgStore.ErrorLogServer(fmt.Errorf("用户`%d`未监听`%s`频道，不能发送消息", c.Id, message.ChannelId))
			continue
		}*/
		if string(strByte) != "" {
			c.hub.broadcast <- message // 转发读取到的channel消息
		} else {
			msgStore.ErrorLogServer(fmt.Errorf("strByte 数据为空"))
		}

	}
}
