package xerr

var message map[uint32]string
var msg map[int]string

func init() {
	message = make(map[uint32]string)
	message[OK] = "SUCCESS"
	message[ERROR] = "ERROR"
	message[SERVER_COMMON_ERROR] = "服务器开小差啦,稍后再来试一试"
	message[REUQES_PARAM_ERROR] = "参数错误"
	message[TOKEN_EXPIRE_ERROR] = "token失效，请重新登陆"
	message[TOKEN_GENERATE_ERROR] = "生成token失败"
	message[DB_ERROR] = "数据库繁忙,请稍后再试"
	message[ErrUserAlreadyRegisterError] = "用户已经注册过了"
	message[ErrGenerateTokenError] = "生成token失败"
	message[ErrUsernamePwdError] = "账号或密码不正确"
	message[ErrUserNoExistsError] = "用户不存在"

}

func MapErrMsg(errcode uint32) string {
	if msg, ok := message[errcode]; ok {
		return msg
	} else {
		return "服务器开小差啦,稍后再来试一试"
	}
}

func IsCodeErr(errcode uint32) bool {
	if _, ok := message[errcode]; ok {
		return true
	} else {
		return false
	}
}
