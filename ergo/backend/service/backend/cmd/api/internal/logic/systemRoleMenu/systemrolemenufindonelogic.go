package logic

import (
	"backend/common/errorx"
	"backend/service/backend/cmd/api/internal/svc"
	"backend/service/backend/cmd/api/internal/types"
	"context"
	"fmt"
	"github.com/zeromicro/go-zero/core/logx"
)

type SystemRoleMenuFindOneLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

// 角色菜单关系 findone
func NewSystemRoleMenuFindOneLogic(ctx context.Context, svcCtx *svc.ServiceContext) SystemRoleMenuFindOneLogic {
	return SystemRoleMenuFindOneLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *SystemRoleMenuFindOneLogic) SystemRoleMenuFindOne(req types.SystemRoleMenuDelReq) (*types.SystemRoleMenuReply, error) {
	one, err := l.svcCtx.SystemRoleMenusModel.FindOne(req.Id)
	if err != nil {
		return nil, errorx.NewCodeError(201, fmt.Sprintf("%v", err), "")
	}
	data := make(map[string]interface{})
	data["item"] = one

	return nil, errorx.NewCodeError(200, fmt.Sprintf("%s", "获取成功"), data)
}
