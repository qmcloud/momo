package logic

import (
	"backend/common/errorx"
	"backend/service/backend/cmd/api/internal/svc"
	"backend/service/backend/cmd/api/internal/types"
	"context"
	"fmt"
	"github.com/zeromicro/go-zero/core/logx"
)

type SystemRoleApiDeleteLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

// 角色Api关系 delete
func NewSystemRoleApiDeleteLogic(ctx context.Context, svcCtx *svc.ServiceContext) SystemRoleApiDeleteLogic {
	return SystemRoleApiDeleteLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *SystemRoleApiDeleteLogic) SystemRoleApiDelete(req types.SystemRoleApiDelReq) (*types.SystemRoleApiReply, error) {
	err := l.svcCtx.SystemRoleApisModel.Delete(req.Id)
	if err != nil {
		return nil, errorx.NewCodeError(201, fmt.Sprintf("%v", err), "")
	}

	return nil, errorx.NewCodeError(200, fmt.Sprintf("编号[%d]删除成功", req.Id), "")
}
