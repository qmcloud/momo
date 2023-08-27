package imsvc

import "backend/service/im/cmd/api/internal/chat"

func JoinChannelIds(uid string, channelIds ...string) {
	chart.JoinChannelIds(uid, channelIds...)
}

func SendMessageToUid(uid string, toUId uint64, msg string, tp uint8) {
	chart.SendMessageToUid(uid, toUId, msg, tp)
}

func SendMessageToUids(uid string, msg string, tp uint8, toUIds ...uint64) {
	chart.SendMessageToUids(uid, msg, tp, toUIds...)
}

func SendMessageToChannelIds(uid string, msg string, tp uint8, channelIds ...string) {
	chart.SendMessageToChannelIds(uid, msg, tp, channelIds...)
}
