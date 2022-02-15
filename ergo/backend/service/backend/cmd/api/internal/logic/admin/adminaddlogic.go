package logic

import (
	"backend/common/errorx"
	"backend/common/utils"
	"backend/service/backend/model"
	"context"
	"fmt"
	"github.com/jinzhu/copier"
	"time"

	"backend/service/backend/cmd/api/internal/svc"
	"backend/service/backend/cmd/api/internal/types"

	"github.com/zeromicro/go-zero/core/logx"
)

type AdminAddLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

func NewAdminAddLogic(ctx context.Context, svcCtx *svc.ServiceContext) AdminAddLogic {
	return AdminAddLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *AdminAddLogic) AdminAdd(req types.AdminPostReq) (*types.AdminReply, error) {
	//检查是否重名
	checkUser, err2 := l.svcCtx.SystemUserModel.FindOneByUserName(req.UserName)
	fmt.Println("----------------checkUser1------------")
	fmt.Println(checkUser)
	fmt.Println(req.UserName)
	fmt.Println(req.Id)
	fmt.Println(err2)
	fmt.Println("----------------checkUser2------------")
	if checkUser.Id > 0 && err2 == nil {
		return nil, errorx.NewCodeError(201, "用户名已经存在", "")
	}

	var admin model.SystemUser
	copier.Copy(&admin, &req)
	admin.Password = utils.MD5V(req.Password)
	defaultLoginTime := "2006-01-02 15:04:05"
	location, _ := time.ParseInLocation(defaultLoginTime, defaultLoginTime, time.Local)
	admin.LoginDate = location

	_, err := l.svcCtx.SystemUserModel.Insert(admin)
	if err != nil {
		return nil, errorx.NewCodeError(202, fmt.Sprintf("%v", err), "")
	}

	return nil, errorx.NewCodeError(200, "添加成功", "")
}
