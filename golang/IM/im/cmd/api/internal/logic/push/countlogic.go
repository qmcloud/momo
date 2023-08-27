package push

import (
	"context"

	"backend/service/im/cmd/api/internal/svc"
	"backend/service/im/cmd/api/internal/types"

	"github.com/zeromicro/go-zero/core/logx"
)

type CountLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

func NewCountLogic(ctx context.Context, svcCtx *svc.ServiceContext) *CountLogic {
	return &CountLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *CountLogic) Count(req *types.FormCount) (resp *types.PushResp, err error) {
	// todo: add your logic here and delete this line

	return
}
