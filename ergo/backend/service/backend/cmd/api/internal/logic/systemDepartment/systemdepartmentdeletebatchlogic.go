package logic

import (
	"backend/common/errorx"
	"backend/service/backend/cmd/api/internal/svc"
	"backend/service/backend/cmd/api/internal/types"
	"context"
	"fmt"
	"github.com/zeromicro/go-zero/core/logx"
)

type SystemDepartmentDeleteBatchLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

// 部门管理 deletebatch
func NewSystemDepartmentDeleteBatchLogic(ctx context.Context, svcCtx *svc.ServiceContext) SystemDepartmentDeleteBatchLogic {
	return SystemDepartmentDeleteBatchLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *SystemDepartmentDeleteBatchLogic) SystemDepartmentDeleteBatch(req types.SystemDepartmentDelBatchReq) (*types.SystemDepartmentReply, error) {
	err := l.svcCtx.SystemDepartmentsModel.DeleteBatch(req.Ids)
	if err != nil {
		return nil, errorx.NewCodeError(201, fmt.Sprintf("%v", err), "")
	}

	return nil, errorx.NewCodeError(200, fmt.Sprintf("编号[%s]删除成功", req.Ids), "")
}
