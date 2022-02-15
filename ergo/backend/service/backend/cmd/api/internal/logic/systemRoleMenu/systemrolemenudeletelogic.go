package logic

import (
	"backend/common/errorx"
	"backend/service/backend/cmd/api/internal/svc"
	"backend/service/backend/cmd/api/internal/types"
	"context"
	"fmt"
	"github.com/zeromicro/go-zero/core/logx"
)

type SystemRoleMenuDeleteLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

// 角色菜单关系 delete
func NewSystemRoleMenuDeleteLogic(ctx context.Context, svcCtx *svc.ServiceContext) SystemRoleMenuDeleteLogic {
	return SystemRoleMenuDeleteLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *SystemRoleMenuDeleteLogic) SystemRoleMenuDelete(req types.SystemRoleMenuDelReq) (*types.SystemRoleMenuReply, error) {
	err := l.svcCtx.SystemRoleMenusModel.Delete(req.Id)
	if err != nil {
		return nil, errorx.NewCodeError(201, fmt.Sprintf("%v", err), "")
	}

	return nil, errorx.NewCodeError(200, fmt.Sprintf("编号[%d]删除成功", req.Id), "")
}
