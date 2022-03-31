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

type SystemRoleApiAddLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

// 角色Api关系 create
func NewSystemRoleApiAddLogic(ctx context.Context, svcCtx *svc.ServiceContext) SystemRoleApiAddLogic {
	return SystemRoleApiAddLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *SystemRoleApiAddLogic) SystemRoleApiAdd(req types.RoleApisReq) (*types.SystemRoleApiReply, error) {
	//检查是否重名

	var item []model.SystemRoleApis
	copier.Copy(&item, &req.SystemRoleApiPostReqs)

	fmt.Println("-------SystemRoleApiAdd1----------")
	fmt.Println(req)
	fmt.Println(item)
	fmt.Println("-------SystemRoleApiAdd2----------")

	_, err := l.svcCtx.SystemRoleApisModel.Insert(item, req.RoleId)
	if err != nil {
		return nil, errorx.NewCodeError(202, fmt.Sprintf("%v", err), "")
	}

	return nil, errorx.NewCodeError(200, "添加成功", "")
}
