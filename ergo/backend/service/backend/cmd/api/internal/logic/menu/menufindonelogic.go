package logic

import (
	"backend/common/errorx"
	"context"
	"fmt"

	"backend/service/backend/cmd/api/internal/svc"
	"backend/service/backend/cmd/api/internal/types"

	"github.com/zeromicro/go-zero/core/logx"
)

type MenuFindOneLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

func NewMenuFindOneLogic(ctx context.Context, svcCtx *svc.ServiceContext) MenuFindOneLogic {
	return MenuFindOneLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *MenuFindOneLogic) MenuFindOne(req types.MenuDelReq) (*types.MenuReply, error) {
	one, err := l.svcCtx.SystemMenusModel.FindOne(req.Id)
	if err != nil {
		return nil, errorx.NewCodeError(201, fmt.Sprintf("%v", err), "")
	}
	data := make(map[string]interface{})
	data["item"] = one

	return nil, errorx.NewCodeError(200, fmt.Sprintf("%s", "获取成功"), data)
}
