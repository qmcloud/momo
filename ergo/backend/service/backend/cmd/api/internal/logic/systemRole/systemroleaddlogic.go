package logic

import (
	"backend/common/errorx"
	"backend/service/backend/cmd/api/internal/svc"
	"backend/service/backend/cmd/api/internal/types"
	"backend/service/backend/model"
	"context"
	"fmt"
	"github.com/jinzhu/copier"
	"github.com/zeromicro/go-zero/core/logx"
)

type SystemRoleAddLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

// 角色管理 create
func NewSystemRoleAddLogic(ctx context.Context, svcCtx *svc.ServiceContext) SystemRoleAddLogic {
	return SystemRoleAddLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *SystemRoleAddLogic) SystemRoleAdd(req types.SystemRolePostReq) (*types.SystemRoleReply, error) {
	//检查是否重名
	checkItem, err2 := l.svcCtx.SystemRolesModel.CheckDuplicatePath(req.Name)
	if checkItem.Id > 0 && err2 == nil {
		return nil, errorx.NewCodeError(201, "已经存在", "")
	}

	var item model.SystemRoles
	copier.Copy(&item, &req)

	_, err := l.svcCtx.SystemRolesModel.Insert(item)
	if err != nil {
		return nil, errorx.NewCodeError(202, fmt.Sprintf("%v", err), "")
	}

	return nil, errorx.NewCodeError(200, "添加成功", "")
}
