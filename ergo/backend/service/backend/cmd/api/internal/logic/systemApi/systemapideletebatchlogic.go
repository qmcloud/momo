package logic

import (
	"backend/common/errorx"
	"backend/service/backend/cmd/api/internal/svc"
	"backend/service/backend/cmd/api/internal/types"
	"context"
	"fmt"
	"github.com/zeromicro/go-zero/core/logx"
)

type SystemApiDeleteBatchLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

// Api管理 deletebatch
func NewSystemApiDeleteBatchLogic(ctx context.Context, svcCtx *svc.ServiceContext) SystemApiDeleteBatchLogic {
	return SystemApiDeleteBatchLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *SystemApiDeleteBatchLogic) SystemApiDeleteBatch(req types.SystemApiDelBatchReq) (*types.SystemApiReply, error) {
	err := l.svcCtx.SystemApisModel.DeleteBatch(req.Ids)
	if err != nil {
		return nil, errorx.NewCodeError(201, fmt.Sprintf("%v", err), "")
	}

	return nil, errorx.NewCodeError(200, fmt.Sprintf("编号[%s]删除成功", req.Ids), "")
}
