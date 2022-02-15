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

type SystemApiAddLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

// Api管理 create
func NewSystemApiAddLogic(ctx context.Context, svcCtx *svc.ServiceContext) SystemApiAddLogic {
	return SystemApiAddLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *SystemApiAddLogic) SystemApiAdd(req types.SystemApiPostReq) (*types.SystemApiReply, error) {
	//检查是否重名
	checkItem, err2 := l.svcCtx.SystemApisModel.CheckDuplicatePath(req.Path)
	if checkItem.Id > 0 && err2 == nil {
		return nil, errorx.NewCodeError(201, "已经存在", "")
	}

	var item model.SystemApis
	copier.Copy(&item, &req)

	_, err := l.svcCtx.SystemApisModel.Insert(item)
	if err != nil {
		return nil, errorx.NewCodeError(202, fmt.Sprintf("%v", err), "")
	}

	return nil, errorx.NewCodeError(200, "添加成功", "")
}
