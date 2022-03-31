package logic

import (
	"backend/common/errorx"
	"backend/service/backend/cmd/api/internal/svc"
	"backend/service/backend/cmd/api/internal/types"
	"context"
	"fmt"
	"github.com/zeromicro/go-zero/core/logx"
)

type SystemRoleMenusByRoleIdLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

func NewSystemRoleMenusByRoleIdLogic(ctx context.Context, svcCtx *svc.ServiceContext) SystemRoleMenusByRoleIdLogic {
	return SystemRoleMenusByRoleIdLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *SystemRoleMenusByRoleIdLogic) SystemRoleMenusByRoleId(req types.SystemRoleMenuAddReq) (*types.SystemRoleMenuReply, error) {
	list, err := l.svcCtx.SystemRoleMenusModel.ListByRoleId(req.RoleId)
	if err != nil {
		return nil, errorx.NewCodeError(201, fmt.Sprintf("%v", err), "")
	}
	data := make(map[string]interface{})
	//data["list"] = list
	menuIds := ""
	for _, v := range list {
		if menuIds == "" {
			menuIds += fmt.Sprintf("%v", v.MenuId)
		} else {
			menuIds += fmt.Sprintf(",%v", v.MenuId)
		}
	}
	// 防止查询所有
	if menuIds == "" {
		menuIds = "0"
	}
	//fmt.Println("----------menuIds>", menuIds)
	tree, _ := l.svcCtx.SystemMenusModel.Tree(menuIds)
	data["list"] = tree
	return nil, errorx.NewCodeError(200, "ok", data)
}
