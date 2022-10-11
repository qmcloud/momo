package chart

func JoinChannelIds(uid string, channelIds ...string) error {
	return nil
}

func UnJoinChannelIds(uid uint64, channelIds ...string) error {
	return nil
}

func SendMessageToUid(uid string, toUId uint64, msg string, tp uint8) {
	return
}

func SendMessageToUids(uid string, msg string, tp uint8, toUIds ...uint64) {
	return
}

func SendMessageToChannelIds(uid string, msg string, tp uint8, channelIds ...string) map[string]bool {

	return nil
}

func sendMessage(c *Client, msg Message) error {
	return nil
}
