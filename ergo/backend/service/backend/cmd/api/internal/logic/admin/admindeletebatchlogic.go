package logic

import (
	"backend/common/errorx"
	"context"
	"fmt"

	"backend/service/backend/cmd/api/internal/svc"
	"backend/service/backend/cmd/api/internal/types"

	"github.com/zeromicro/go-zero/core/logx"
)

type AdminDeleteBatchLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

func NewAdminDeleteBatchLogic(ctx context.Context, svcCtx *svc.ServiceContext) AdminDeleteBatchLogic {
	return AdminDeleteBatchLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *AdminDeleteBatchLogic) AdminDeleteBatch(req types.AdminDelBatchReq) (*types.AdminReply, error) {
	err := l.svcCtx.SystemUserModel.DeleteBatch(req.Ids)
	if err != nil {
		return nil, errorx.NewCodeError(201, fmt.Sprintf("%v", err), "")
	}

	return nil, errorx.NewCodeError(200, fmt.Sprintf("编号[%s]删除成功", req.Ids), "")
}
