package xerr

//成功返回
const OK uint32 = 200
const ERROR uint32 = 500

/**(前3位代表业务,后三位代表具体功能)**/

//全局错误码
const SERVER_COMMON_ERROR uint32 = 100001
const REUQES_PARAM_ERROR uint32 = 100002
const TOKEN_EXPIRE_ERROR uint32 = 100003
const TOKEN_GENERATE_ERROR uint32 = 100004
const DB_ERROR uint32 = 100005
const DB_UPDATE_AFFECTED_ZERO_ERROR uint32 = 100006

//用户模块
//3字头用户模块
const ErrUserAlreadyRegisterError uint32 = 300001
const ErrGenerateTokenError uint32 = 300002
const ErrUsernamePwdError uint32 = 300003
const ErrUserNoExistsError uint32 = 300004
