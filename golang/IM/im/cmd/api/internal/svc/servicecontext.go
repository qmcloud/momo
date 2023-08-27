package svc

import (
	"backend/service/im/cmd/api/internal/config"
	"backend/service/im/cmd/rpc/ws"
	"backend/service/im/model/groups"
	"backend/service/im/model/groupusers"
	"backend/service/im/model/hasusers"
	"backend/service/im/model/messages"
	"backend/service/im/model/notices"
	"backend/service/im/model/sendqueue"
	"backend/service/im/model/user"
	"fmt"
	"github.com/google/uuid"
	"github.com/smallnest/rpcx/client"
	"github.com/zeromicro/go-zero/core/stores/redis"
	"github.com/zeromicro/go-zero/core/stores/sqlx"
	"github.com/zeromicro/go-zero/zrpc"
	"sync"
)

type ServiceContext struct {
	Config         config.Config
	RedisClient    *redis.Redis
	UserModel      user.UsersModel
	UserUsersModel hasusers.UserUsersModel
	NoticeModel    notices.NoticesModel
	MessageModel   messages.MessagesModel
	SendQueueModel sendqueue.SendQueuesModel
	GroupModel     groups.GroupsModel
	GroupUserModel groupusers.GroupUsersModel
	WsRpcConf      ws.Ws
	ServerId       string
}

var LogicRpcClient client.XClient
var once sync.Once

type RpcLogic struct {
}

var RpcLogicObj *RpcLogic

func NewServiceContext(c config.Config) *ServiceContext {
	conn := sqlx.NewMysql(c.Mysql.DataSource)
	ServerId := fmt.Sprintf("%s-%s", "tcp", uuid.New().String())
	return &ServiceContext{
		Config: c,
		RedisClient: redis.New(c.Redis.Host, func(r *redis.Redis) {
			r.Type = c.Redis.Type
			r.Pass = c.Redis.Pass
		}),
		UserModel:      user.NewUsersModel(conn, c.CacheRedis),
		UserUsersModel: hasusers.NewUserUsersModel(conn),
		NoticeModel:    notices.NewNoticesModel(conn),
		MessageModel:   messages.NewMessagesModel(conn),
		SendQueueModel: sendqueue.NewSendQueuesModel(conn),
		GroupModel:     groups.NewGroupsModel(conn),
		GroupUserModel: groupusers.NewGroupUsersModel(conn),
		WsRpcConf:      ws.NewWs(zrpc.MustNewClient(c.WsRpcConf)),
		ServerId:       ServerId,
	}
}
