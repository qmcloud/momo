/**
 * Created by yson
 * Date: 2022-06-01
 * Time: 15:18
 */
package imsvc

import (
	"backend/service/im/cmd/rpc/pb"
	"github.com/gorilla/websocket"
	"net"
)

//in fact, Channel it's a user Connect session
type Channel struct {
	Room      *Room
	Next      *Channel
	Prev      *Channel
	broadcast chan *pb.Msg
	userId    int64
	conn      *websocket.Conn
	connTcp   *net.TCPConn
}

func NewChannel(size int) (c *Channel) {
	c = new(Channel)
	c.broadcast = make(chan *pb.Msg, size)
	c.Next = nil
	c.Prev = nil
	return
}

func (ch *Channel) Push(msg *pb.Msg) (err error) {
	select {
	case ch.broadcast <- msg:
	default:
	}
	return
}
