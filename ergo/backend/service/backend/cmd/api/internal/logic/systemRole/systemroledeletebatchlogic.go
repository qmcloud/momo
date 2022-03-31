package logic

import (
	"backend/common/errorx"
	"backend/service/backend/cmd/api/internal/svc"
	"backend/service/backend/cmd/api/internal/types"
	"context"
	"fmt"
	"github.com/zeromicro/go-zero/core/logx"
)

type SystemRoleDeleteBatchLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

// 角色管理 deletebatch
func NewSystemRoleDeleteBatchLogic(ctx context.Context, svcCtx *svc.ServiceContext) SystemRoleDeleteBatchLogic {
	return SystemRoleDeleteBatchLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *SystemRoleDeleteBatchLogic) SystemRoleDeleteBatch(req types.SystemRoleDelBatchReq) (*types.SystemRoleReply, error) {
	err := l.svcCtx.SystemRolesModel.DeleteBatch(req.Ids)
	if err != nil {
		return nil, errorx.NewCodeError(201, fmt.Sprintf("%v", err), "")
	}

	return nil, errorx.NewCodeError(200, fmt.Sprintf("编号[%s]删除成功", req.Ids), "")
}
