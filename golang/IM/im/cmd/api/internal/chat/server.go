package chart

import (
	"backend/service/im/cmd/api/internal/svc"
	"fmt"
	"log"
	"net/http"
	"time"
)

func NewServer(ctx *svc.ServiceContext, w http.ResponseWriter, r *http.Request, clientId uint64, detail interface{}) {
	if globHub == nil {
		globHub = newServer()
		go globHub.run(ctx)
	}
	if clients == nil {
		clients = make(map[string]*Client)
	}
	/*if msgStore == nil {
		msgStore = store
	}*/
	runWs(globHub, w, r, clientId, detail)
}

func runWs(hub *Hub, w http.ResponseWriter, r *http.Request, clientId uint64, detail interface{}) {
	conn, err := upGrader.Upgrade(w, r, nil)
	if err != nil {
		fmt.Println("升级get请求错误", err)
		return
	}
	//临时获取uid
	uid := r.URL.Query()
	if len(uid) == 0 {
		log.Println("uid is empty")
		return
		//fmt.Println("id1:", uid["uid"][0])
	}
	client := &Client{hub: hub, conn: conn, send: make(chan []byte, bufSize), Id: clientId, Detail: detail, uid: uid["uid"][0]}
	//连接时休眠1秒  防止刷新页面 先连接后退出
	time.Sleep(time.Duration(1) * time.Second)
	client.hub.register <- client
	go client.ReadMsg()
	go client.WriteMsg()
}
