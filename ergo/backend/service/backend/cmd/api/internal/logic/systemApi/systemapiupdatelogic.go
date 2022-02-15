package logic

import (
	"backend/common/errorx"
	"backend/service/backend/cmd/api/internal/svc"
	"backend/service/backend/cmd/api/internal/types"
	"context"
	"fmt"
	"github.com/jinzhu/copier"
	"github.com/zeromicro/go-zero/core/logx"
)

type SystemApiUpdateLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

// Api管理 update
func NewSystemApiUpdateLogic(ctx context.Context, svcCtx *svc.ServiceContext) SystemApiUpdateLogic {
	return SystemApiUpdateLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *SystemApiUpdateLogic) SystemApiUpdate(req types.SystemApiPostReq) (*types.SystemApiReply, error) {
	oldData, err := l.svcCtx.SystemApisModel.FindOne(req.Id)
	if err != nil {
		return nil, errorx.NewCodeError(201, fmt.Sprintf("%v", err), "")
	}

	copier.Copy(&oldData, &req)
	err = l.svcCtx.SystemApisModel.Update(*oldData)

	if err != nil {
		return nil, errorx.NewCodeError(201, fmt.Sprintf("%v", err), "")
	}

	return nil, errorx.NewCodeError(200, fmt.Sprintf("%s", "修改成功"), "")
}
