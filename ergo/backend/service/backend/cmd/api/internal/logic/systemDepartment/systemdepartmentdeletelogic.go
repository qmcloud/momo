package logic

import (
	"backend/common/errorx"
	"backend/service/backend/cmd/api/internal/svc"
	"backend/service/backend/cmd/api/internal/types"
	"context"
	"fmt"
	"github.com/zeromicro/go-zero/core/logx"
)

type SystemDepartmentDeleteLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

// 部门管理 delete
func NewSystemDepartmentDeleteLogic(ctx context.Context, svcCtx *svc.ServiceContext) SystemDepartmentDeleteLogic {
	return SystemDepartmentDeleteLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *SystemDepartmentDeleteLogic) SystemDepartmentDelete(req types.SystemDepartmentDelReq) (*types.SystemDepartmentReply, error) {
	err := l.svcCtx.SystemDepartmentsModel.Delete(req.Id)
	if err != nil {
		return nil, errorx.NewCodeError(201, fmt.Sprintf("%v", err), "")
	}

	return nil, errorx.NewCodeError(200, fmt.Sprintf("编号[%d]删除成功", req.Id), "")
}
