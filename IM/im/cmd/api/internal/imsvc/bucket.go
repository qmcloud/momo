/**
 * Created by yson
 * Date: 2022-06-01
 * Time: 15:18
 */
package imsvc

import (
	"backend/service/im/cmd/rpc/pb"
	"sync"
	"sync/atomic"
)

type Bucket struct {
	cLock         sync.RWMutex       // protect the channels for chs
	chs           map[int64]*Channel // map sub key to a channel
	bucketOptions BucketOptions
	rooms         map[int64]*Room // bucket room channels
	routines      []chan *pb.PushRoomMsgRequest
	routinesNum   uint64
	broadcast     chan []byte
}

type BucketOptions struct {
	ChannelSize   int
	RoomSize      int
	RoutineAmount uint64
	RoutineSize   int
}

func NewBucket(bucketOptions BucketOptions) (b *Bucket) {
	b = new(Bucket)
	b.chs = make(map[int64]*Channel, bucketOptions.ChannelSize)
	b.bucketOptions = bucketOptions
	b.routines = make([]chan *pb.PushRoomMsgRequest, bucketOptions.RoutineAmount)
	b.rooms = make(map[int64]*Room, bucketOptions.RoomSize)
	for i := uint64(0); i < b.bucketOptions.RoutineAmount; i++ {
		c := make(chan *pb.PushRoomMsgRequest, bucketOptions.RoutineSize)
		b.routines[i] = c
		go b.PushRoom(c)
	}
	return
}

func (b *Bucket) PushRoom(ch chan *pb.PushRoomMsgRequest) {
	for {
		var (
			arg  *pb.PushRoomMsgRequest
			room *Room
		)
		arg = <-ch
		if room = b.Room(arg.RoomId); room != nil {
			room.Push(arg.Msg)
		}
	}
}

func (b *Bucket) Room(rid int64) (room *Room) {
	b.cLock.RLock()
	room, _ = b.rooms[rid]
	b.cLock.RUnlock()
	return
}

func (b *Bucket) Put(userId int64, roomId int64, ch *Channel) (err error) {
	var (
		room *Room
		ok   bool
	)
	b.cLock.Lock()
	if roomId != NoRoom {
		if room, ok = b.rooms[roomId]; !ok {
			room = NewRoom(roomId)
			b.rooms[roomId] = room
		}
		ch.Room = room
	}
	ch.userId = userId
	b.chs[userId] = ch
	b.cLock.Unlock()

	if room != nil {
		err = room.Put(ch)
	}
	return
}

func (b *Bucket) DeleteChannel(ch *Channel) {
	var (
		ok   bool
		room *Room
	)
	b.cLock.RLock()
	if ch, ok = b.chs[ch.userId]; ok {
		room = b.chs[ch.userId].Room
		//delete from bucket
		delete(b.chs, ch.userId)
	}
	if room != nil && room.DeleteChannel(ch) {
		// if room empty delete,will mark room.drop is true
		if room.drop == true {
			delete(b.rooms, room.Id)
		}
	}
	b.cLock.RUnlock()
}

func (b *Bucket) Channel(userId int64) (ch *Channel) {
	b.cLock.RLock()
	ch = b.chs[userId]
	b.cLock.RUnlock()
	return
}

func (b *Bucket) BroadcastRoom(pushRoomMsgReq *pb.PushRoomMsgRequest) {
	num := atomic.AddUint64(&b.routinesNum, 1) % b.bucketOptions.RoutineAmount
	b.routines[num] <- pushRoomMsgReq
}
