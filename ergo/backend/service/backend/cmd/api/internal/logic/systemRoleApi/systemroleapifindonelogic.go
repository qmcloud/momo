package logic

import (
	"backend/common/errorx"
	"backend/service/backend/cmd/api/internal/svc"
	"backend/service/backend/cmd/api/internal/types"
	"context"
	"fmt"
	"github.com/zeromicro/go-zero/core/logx"
)

type SystemRoleApiFindOneLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

// 角色Api关系 findone
func NewSystemRoleApiFindOneLogic(ctx context.Context, svcCtx *svc.ServiceContext) SystemRoleApiFindOneLogic {
	return SystemRoleApiFindOneLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *SystemRoleApiFindOneLogic) SystemRoleApiFindOne(req types.SystemRoleApiDelReq) (*types.SystemRoleApiReply, error) {
	one, err := l.svcCtx.SystemRoleApisModel.FindOne(req.Id)
	if err != nil {
		return nil, errorx.NewCodeError(201, fmt.Sprintf("%v", err), "")
	}
	data := make(map[string]interface{})
	data["item"] = one

	return nil, errorx.NewCodeError(200, fmt.Sprintf("%s", "获取成功"), data)
}
