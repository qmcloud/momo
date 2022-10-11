package chart

const (
	msgTypeSendText  = 1
	msgTypeSendImage = 2
	msgTypeSendAudio = 3
	msgTypeSendVideo = 4

	/*
	 join 主动加入房间
	 leave 主动离开房间
	 new-peer 有人加入房间，通知已经在房间的人
	 peer-leave 有人离开房间，通知已经在房间的人
	 offer 发送offer给对端peer
	 answer发送offer给对端peer
	 candidate 发送candidate给对端peer
	*/

	SIGNAL_TYPE_JOIN       = "join"
	SIGNAL_TYPE_RESP_JOIN  = "resp-join" // 告知加入者对方是谁
	SIGNAL_TYPE_LEAVE      = "leave"
	SIGNAL_TYPE_NEW_PEER   = "new-peer"
	SIGNAL_TYPE_PEER_LEAVE = "peer-leave"
	SIGNAL_TYPE_OFFER      = "offer"
	SIGNAL_TYPE_ANSWER     = "answer"
	SIGNAL_TYPE_CANDIDATE  = "candidate"
	ERR_MSG                = "err-msg"
)

type Message struct {
	ChannelId    string      `json:"channel_id"`    // 管道ID
	ChannelTitle string      `json:"channel_title"` // 管道标题
	UserId       uint64      `json:"user_id"`
	Detail       interface{} `json:"detail"`
	ToUserId     uint64      `json:"to_user_id"`
	Type         uint8       `json:"type"`    // 消息类型
	Content      string      `json:"content"` // 消息内容
	SendTime     string      `json:"send_time"`
	SingleMsg    []byte      `json:"single_msg""` //信令消息
}

//信令结构体
type SingleMessage struct {
	Cmd       string      `json:"cmd"`
	RoomId    string      `json:"roomId"`
	Uid       string      `json:"uid"`
	RemoteUid string      `json:"remoteUid"`
	Msg       interface{} `json:"msg"`
}
type ToSinglejsonMsg struct {
	Cmd       string `json:"cmd"`
	RemoteUid string `json:"remoteUid"`
}
type messageInterface interface {
	SendMessage(msg Message)
	DelaySendMessage(channelId string, msg Message, uids []uint64)
	LoginServer(uid uint64)
	LogoutServer(uid uint64)
	ErrorLogServer(err error)
}

var msgStore messageInterface
