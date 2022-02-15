package logic

import (
	"backend/common/errorx"
	"backend/service/backend/cmd/api/internal/svc"
	"backend/service/backend/cmd/api/internal/types"
	"context"
	"fmt"
	"github.com/zeromicro/go-zero/core/logx"
)

type SystemRoleApisByRoleIdLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

func NewSystemRoleApisByRoleIdLogic(ctx context.Context, svcCtx *svc.ServiceContext) SystemRoleApisByRoleIdLogic {
	return SystemRoleApisByRoleIdLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *SystemRoleApisByRoleIdLogic) SystemRoleApisByRoleId(req types.SystemRoleApiDelReq) (*types.SystemRoleApiReply, error) {
	list, err := l.svcCtx.SystemRoleApisModel.ListByRoleId(req.Id)
	if err != nil {
		return nil, errorx.NewCodeError(201, fmt.Sprintf("%v", err), "")
	}
	data := make(map[string]interface{})
	data["list"] = list
	//menuIds := ""
	//for _, v := range list {
	//	if menuIds == "" {
	//		menuIds += fmt.Sprintf("%v", v.MenuId)
	//	} else {
	//		menuIds += fmt.Sprintf(",%v", v.MenuId)
	//	}
	//}
	//// 防止查询所有
	//if menuIds == "" {
	//	menuIds = "0"
	//}
	////fmt.Println("----------menuIds>", menuIds)
	//tree, _ := l.svcCtx.SystemMenusModel.Tree(menuIds)
	//data["list"] = tree
	return nil, errorx.NewCodeError(200, "ok", data)
}
