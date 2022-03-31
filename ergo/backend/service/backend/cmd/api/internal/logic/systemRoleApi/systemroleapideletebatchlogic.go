package logic

import (
	"backend/common/errorx"
	"backend/service/backend/cmd/api/internal/svc"
	"backend/service/backend/cmd/api/internal/types"
	"context"
	"fmt"
	"github.com/zeromicro/go-zero/core/logx"
)

type SystemRoleApiDeleteBatchLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

// 角色Api关系 deletebatch
func NewSystemRoleApiDeleteBatchLogic(ctx context.Context, svcCtx *svc.ServiceContext) SystemRoleApiDeleteBatchLogic {
	return SystemRoleApiDeleteBatchLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *SystemRoleApiDeleteBatchLogic) SystemRoleApiDeleteBatch(req types.SystemRoleApiDelBatchReq) (*types.SystemRoleApiReply, error) {
	err := l.svcCtx.SystemRoleApisModel.DeleteBatch(req.Ids)
	if err != nil {
		return nil, errorx.NewCodeError(201, fmt.Sprintf("%v", err), "")
	}

	return nil, errorx.NewCodeError(200, fmt.Sprintf("编号[%s]删除成功", req.Ids), "")
}
