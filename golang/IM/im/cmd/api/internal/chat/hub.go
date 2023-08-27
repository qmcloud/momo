package chart

import (
	"backend/service/im/cmd/api/internal/svc"
	"encoding/json"
	"fmt"
)

type Hub struct {
	// 所有通道客户端
	clients map[string]*Client
	// 发送消息
	broadcast chan Message
	//信令消息
	singlemsg chan []byte
	// 注册客户端
	register chan *Client
	// 注销客户端
	unregister chan *Client
	//房间映射
	room2uid map[string]string
}

var globHub *Hub

func newServer() *Hub {
	return &Hub{
		clients:    make(map[string]*Client),
		broadcast:  make(chan Message),
		singlemsg:  make(chan []byte),
		register:   make(chan *Client),
		unregister: make(chan *Client),
		room2uid:   make(map[string]string),
	}
}

type TojsonMsg struct {
	Cmd       string `json:"cmd"`
	RemoteUid string `json:"remoteUid"`
}

type ToErrMsg struct {
	Cmd       string `json:"cmd"`
	RemoteUid string `json:"remoteUid"`
	Msg       string `json:"msg"`
}

func (h *Hub) run(ctx *svc.ServiceContext) {
	for {
		select {
		case client := <-h.register: // 登录
			if _, ok := clients[client.uid]; !ok {
				clients[client.uid] = client
			}
			fmt.Println(clients)
		case client := <-h.unregister: // 注销 / 退出
			// 退出聊天服务
			if _, ok := h.room2uid[client.uid]; ok {
				fmt.Println(h.room2uid[client.uid])
				roomid := h.room2uid[client.uid]
				if roomid != "" {
					isdel, _ := ctx.RedisClient.Hdel("room_"+roomid, client.uid)
					if !isdel {
						fmt.Println("del room_" + roomid + " err")
					}
				}
				delete(h.room2uid, client.uid)
			}
			if _, ok := h.clients[client.uid]; ok {
				delete(h.clients, client.uid)
				close(client.send)
			}

		case message := <-h.broadcast: // 接受普通消息
			//信令服务
			SingJson, err := json.Marshal(message)
			if err != nil {
				fmt.Println("Marshal SingleMessage is Error")
			}
			if SingJson != nil {
				msg := Message{}
				err := json.Unmarshal(SingJson, &msg)
				if err != nil {
					fmt.Println("Unmarshal SingleMessage is Error")
				}
				if msg.SingleMsg != nil {
					jsonMsg := SingleMessage{}
					err = json.Unmarshal(msg.SingleMsg, &jsonMsg)
					if err != nil {
						fmt.Println("Unmarshal SingleMsg is Error")
					}
					switch jsonMsg.Cmd {
					case SIGNAL_TYPE_JOIN:
						roomTable, err := ctx.RedisClient.Hgetall("room_" + jsonMsg.RoomId)
						if err != nil {
							fmt.Println("roomTable Hset is Error")
						}
						fmt.Println(len(roomTable))
						if len(roomTable) > 2 {
							fmt.Println(" roomTable > 2")
							toMemsg := ToErrMsg{Cmd: ERR_MSG, RemoteUid: jsonMsg.Uid, Msg: "join room err: in room count >2"}
							msg1, _ := json.Marshal(toMemsg)
							clients[jsonMsg.Uid].send <- msg1
							break
						}
						h.room2uid[jsonMsg.Uid] = jsonMsg.RoomId
						JoinInfo := make(map[string]*Client)
						JoinInfo[jsonMsg.Uid] = clients[jsonMsg.Uid]
						str2json, _ := json.Marshal(JoinInfo)
						//加入房间
						err = ctx.RedisClient.Hset("room_"+jsonMsg.RoomId, jsonMsg.Uid, string(str2json))
						if err != nil {
							fmt.Println("Redis Hset is Error")
						}
						for k := range roomTable {
							fmt.Println(k, jsonMsg.Uid)
							if k != jsonMsg.Uid {
								toMemsg := TojsonMsg{Cmd: SIGNAL_TYPE_NEW_PEER, RemoteUid: jsonMsg.Uid}
								msg1, _ := json.Marshal(toMemsg)
								if _, ok := clients[k]; ok {
									clients[k].send <- msg1
								} else {
									msgStore.ErrorLogServer(fmt.Errorf(" roomTable have no info"))
								}

								toUsmsg := TojsonMsg{Cmd: SIGNAL_TYPE_RESP_JOIN, RemoteUid: k}
								msg2, _ := json.Marshal(toUsmsg)
								if _, ok := clients[jsonMsg.Uid]; ok {
									clients[jsonMsg.Uid].send <- msg2
								} else {
									msgStore.ErrorLogServer(fmt.Errorf(" roomTable have no info"))
								}

							}
						}

					case SIGNAL_TYPE_LEAVE:
						//离开
						isdel, _ := ctx.RedisClient.Hdel("room_"+jsonMsg.RoomId, jsonMsg.Uid)
						if !isdel {
							fmt.Println("Hdel err")
						}
						toMemsg := TojsonMsg{Cmd: SIGNAL_TYPE_PEER_LEAVE, RemoteUid: jsonMsg.Uid}
						msg1, _ := json.Marshal(toMemsg)
						roomTable, _ := ctx.RedisClient.Hgetall("room_" + jsonMsg.RoomId)
						for k := range roomTable {
							if k != jsonMsg.Uid {
								select {
								case clients[k].send <- msg1:
								}
							}
						}
					case SIGNAL_TYPE_OFFER:
						//发送offer 给对方
						remoteuid := jsonMsg.RemoteUid
						if remoteuid != "" {
							resMessage := SingleMessage{
								Cmd:       SIGNAL_TYPE_OFFER,
								Uid:       jsonMsg.Uid,
								RoomId:    jsonMsg.RoomId,
								RemoteUid: remoteuid,
								Msg:       jsonMsg.Msg,
							}
							OfferMessage, _ := json.Marshal(resMessage)
							if _, ok := clients[remoteuid]; ok {
								clients[remoteuid].send <- OfferMessage
							} else {
								msgStore.ErrorLogServer(fmt.Errorf(" OfferMessage send err"))
							}
						}
					case SIGNAL_TYPE_ANSWER:
						remoteuid := jsonMsg.RemoteUid
						if remoteuid != "" {
							resMessage := SingleMessage{
								Cmd:       SIGNAL_TYPE_ANSWER,
								Uid:       jsonMsg.Uid,
								RoomId:    jsonMsg.RoomId,
								RemoteUid: remoteuid,
								Msg:       jsonMsg.Msg,
							}
							OfferMessage, _ := json.Marshal(resMessage)
							if _, ok := clients[remoteuid]; ok {
								clients[remoteuid].send <- OfferMessage
							} else {
								msgStore.ErrorLogServer(fmt.Errorf(" ANSWERMessage send err"))
							}

						}
					case SIGNAL_TYPE_CANDIDATE:
						remoteUid := jsonMsg.RemoteUid
						if remoteUid != "" {
							//把数据发送给对方
							if _, ok := clients[remoteUid]; ok {
								clients[remoteUid].send <- msg.SingleMsg
							} else {
								msgStore.ErrorLogServer(fmt.Errorf(" CANDIDATEMessage send err"))
							}
						} else {
							msgStore.ErrorLogServer(fmt.Errorf("can't find remoteUid： " + remoteUid))
						}
					default:
						fmt.Println("jsonMsg err", jsonMsg)
					}
				}
			}
		}
	}
}
