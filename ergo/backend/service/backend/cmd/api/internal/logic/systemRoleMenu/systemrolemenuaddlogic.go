package logic

import (
	"backend/common/errorx"
	"backend/service/backend/cmd/api/internal/svc"
	"backend/service/backend/cmd/api/internal/types"
	"context"
	"fmt"
	"github.com/zeromicro/go-zero/core/logx"
)

type SystemRoleMenuAddLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

// 角色菜单关系 create
func NewSystemRoleMenuAddLogic(ctx context.Context, svcCtx *svc.ServiceContext) SystemRoleMenuAddLogic {
	return SystemRoleMenuAddLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *SystemRoleMenuAddLogic) SystemRoleMenuAdd(req types.SystemRoleMenuAddReq) (*types.SystemRoleMenuReply, error) {
	_, err := l.svcCtx.SystemRoleMenusModel.Insert(req.MenuIds, req.RoleId)
	if err != nil {
		return nil, errorx.NewCodeError(202, fmt.Sprintf("%v", err), "")
	}

	return nil, errorx.NewCodeError(200, "添加成功", "")
}
