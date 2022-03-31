package logic

import (
	"backend/common/errorx"
	"backend/service/backend/cmd/api/internal/svc"
	"backend/service/backend/cmd/api/internal/types"
	"context"
	"fmt"
	"github.com/zeromicro/go-zero/core/logx"
)

type SystemRoleApiUpdateLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

// 角色Api关系 update
func NewSystemRoleApiUpdateLogic(ctx context.Context, svcCtx *svc.ServiceContext) SystemRoleApiUpdateLogic {
	return SystemRoleApiUpdateLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *SystemRoleApiUpdateLogic) SystemRoleApiUpdate(req types.SystemRoleApiPostReq) (*types.SystemRoleApiReply, error) {
	//oldData, err := l.svcCtx.SystemRoleApisModel.FindOne(req.Id)
	//if err != nil {
	//	return nil, errorx.NewCodeError(201, fmt.Sprintf("%v", err), "")
	//}
	//
	//copier.Copy(&oldData, &req)
	//err = l.svcCtx.SystemRoleApisModel.Update(*oldData)
	//
	//if err != nil {
	//	return nil, errorx.NewCodeError(201, fmt.Sprintf("%v", err), "")
	//}

	return nil, errorx.NewCodeError(200, fmt.Sprintf("%s", "修改成功"), "")
}
