package svc

import (
	"backend/service/backend/cmd/api/internal/config"
	"backend/service/backend/cmd/api/internal/middleware"
	"backend/service/backend/cmd/rpc/systemuserget/systemusergeter"
	"backend/service/backend/model"
	"github.com/zeromicro/go-zero/core/stores/sqlx"
	"github.com/zeromicro/go-zero/rest"
	"github.com/zeromicro/go-zero/zrpc"
)

type ServiceContext struct {
	Config                 config.Config
	SystemUserModel        model.SystemUserModel
	SystemMenusModel       model.SystemMenusModel
	SystemApisModel        model.SystemApisModel
	SystemRolesModel       model.SystemRolesModel
	SystemDepartmentsModel model.SystemDepartmentsModel
	SystemRoleMenusModel   model.SystemRoleMenusModel
	SystemRoleApisModel    model.SystemRoleApisModel
	CheckLogin             rest.Middleware
	//rpc
	Systemusergeter systemusergeter.Systemusergeter
}

func NewServiceContext(c config.Config) *ServiceContext {
	conn := sqlx.NewMysql(c.Mysql.DataSource)
	return &ServiceContext{
		Config:                 c,
		SystemUserModel:        model.NewSystemUserModel(conn, c.CacheRedis),
		SystemMenusModel:       model.NewSystemMenusModel(conn, c.CacheRedis),
		SystemApisModel:        model.NewSystemApisModel(conn, c.CacheRedis),
		SystemRolesModel:       model.NewSystemRolesModel(conn, c.CacheRedis),
		SystemDepartmentsModel: model.NewSystemDepartmentsModel(conn, c.CacheRedis),
		SystemRoleMenusModel:   model.NewSystemRoleMenusModel(conn, c.CacheRedis),
		SystemRoleApisModel:    model.NewSystemRoleApisModel(conn, c.CacheRedis),
		CheckLogin:             middleware.NewCheckLoginMiddleware(c.Mysql.DataSource).Handle,
		//rpc
		Systemusergeter: systemusergeter.NewSystemusergeter(zrpc.MustNewClient(c.GetsystemuserRpc)),
	}
}
