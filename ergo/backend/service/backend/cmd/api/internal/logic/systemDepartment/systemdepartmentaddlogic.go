package logic

import (
	"backend/common/errorx"
	"backend/service/backend/cmd/api/internal/svc"
	"backend/service/backend/cmd/api/internal/types"
	"backend/service/backend/model"
	"context"
	"fmt"
	"github.com/jinzhu/copier"
	"github.com/zeromicro/go-zero/core/logx"
)

type SystemDepartmentAddLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

// 部门管理 create
func NewSystemDepartmentAddLogic(ctx context.Context, svcCtx *svc.ServiceContext) SystemDepartmentAddLogic {
	return SystemDepartmentAddLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *SystemDepartmentAddLogic) SystemDepartmentAdd(req types.SystemDepartmentPostReq) (*types.SystemDepartmentReply, error) {
	//检查是否重名
	checkItem, err2 := l.svcCtx.SystemDepartmentsModel.CheckDuplicate(req.Name)
	if checkItem.Id > 0 && err2 == nil {
		return nil, errorx.NewCodeError(201, "已经存在", "")
	}

	var item model.SystemDepartments
	copier.Copy(&item, &req)

	_, err := l.svcCtx.SystemDepartmentsModel.Insert(item)
	if err != nil {
		return nil, errorx.NewCodeError(202, fmt.Sprintf("%v", err), "")
	}

	return nil, errorx.NewCodeError(200, "添加成功", "")
}
