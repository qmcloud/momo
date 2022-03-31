package svc

import (
	"backend/service/backend/cmd/rpc/systemuserget/internal/config"
	"backend/service/backend/model"
	"github.com/zeromicro/go-zero/core/stores/sqlx"
)

type ServiceContext struct {
	Config          config.Config
	SystemUserModel model.SystemUserModel
}

func NewServiceContext(c config.Config) *ServiceContext {
	conn := sqlx.NewMysql(c.Mysql.DataSource)
	return &ServiceContext{
		Config:          c,
		SystemUserModel: model.NewSystemUserModel(conn, c.CacheRedis),
	}
}
