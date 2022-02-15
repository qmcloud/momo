package logic

import (
	"backend/common/errorx"
	"backend/service/backend/cmd/api/internal/svc"
	"backend/service/backend/cmd/api/internal/types"
	"context"
	"fmt"
	"github.com/zeromicro/go-zero/core/logx"
)

type SystemDepartmentFindOneLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

// 部门管理 findone
func NewSystemDepartmentFindOneLogic(ctx context.Context, svcCtx *svc.ServiceContext) SystemDepartmentFindOneLogic {
	return SystemDepartmentFindOneLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *SystemDepartmentFindOneLogic) SystemDepartmentFindOne(req types.SystemDepartmentDelReq) (*types.SystemDepartmentReply, error) {
	one, err := l.svcCtx.SystemDepartmentsModel.FindOne(req.Id)
	if err != nil {
		return nil, errorx.NewCodeError(201, fmt.Sprintf("%v", err), "")
	}
	data := make(map[string]interface{})
	data["item"] = one

	return nil, errorx.NewCodeError(200, fmt.Sprintf("%s", "获取成功"), data)
}
