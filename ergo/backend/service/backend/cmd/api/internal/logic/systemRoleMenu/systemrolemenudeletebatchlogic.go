package logic

import (
	"backend/common/errorx"
	"backend/service/backend/cmd/api/internal/svc"
	"backend/service/backend/cmd/api/internal/types"
	"context"
	"fmt"
	"github.com/zeromicro/go-zero/core/logx"
)

type SystemRoleMenuDeleteBatchLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

// 角色菜单关系 deletebatch
func NewSystemRoleMenuDeleteBatchLogic(ctx context.Context, svcCtx *svc.ServiceContext) SystemRoleMenuDeleteBatchLogic {
	return SystemRoleMenuDeleteBatchLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *SystemRoleMenuDeleteBatchLogic) SystemRoleMenuDeleteBatch(req types.SystemRoleMenuDelBatchReq) (*types.SystemRoleMenuReply, error) {
	err := l.svcCtx.SystemRoleMenusModel.DeleteBatch(req.Ids)
	if err != nil {
		return nil, errorx.NewCodeError(201, fmt.Sprintf("%v", err), "")
	}

	return nil, errorx.NewCodeError(200, fmt.Sprintf("编号[%s]删除成功", req.Ids), "")
}
