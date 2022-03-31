package logic

import (
	"backend/common/errorx"
	"backend/common/utils"
	"backend/service/backend/model"
	"context"
	"fmt"

	"backend/service/backend/cmd/api/internal/svc"
	"backend/service/backend/cmd/api/internal/types"

	"github.com/zeromicro/go-zero/core/logx"
)

type AdminInitLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

func NewAdminInitLogic(ctx context.Context, svcCtx *svc.ServiceContext) AdminInitLogic {
	return AdminInitLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *AdminInitLogic) AdminInit() (*types.AdminReply, error) {
	username := "admin"
	password := "123456"
	userinfo, err := l.svcCtx.SystemUserModel.FindOneByUserName(username)
	switch err {
	case nil:
	case model.ErrNotFound:
		userinfo.UserName = username
		userinfo.Password = utils.MD5V(password)
		l.svcCtx.SystemUserModel.Insert(userinfo)
		return nil, errorx.NewCodeError(200, fmt.Sprintf("添加用户%s,密码%s", username, password), "")
	default:
		return nil, err
	}
	//重置密码
	userinfo.Password = utils.MD5V(password)
	l.svcCtx.SystemUserModel.Update(userinfo)
	return nil, errorx.NewCodeError(200, fmt.Sprintf("重置用户%s,密码%s", username, password), "")
}
