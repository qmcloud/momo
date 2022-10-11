package push

import (
	"context"

	"backend/service/im/cmd/api/internal/svc"
	"backend/service/im/cmd/api/internal/types"

	"github.com/zeromicro/go-zero/core/logx"
)

type PushLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

func NewPushLogic(ctx context.Context, svcCtx *svc.ServiceContext) *PushLogic {
	return &PushLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *PushLogic) Push(req *types.FormPush) (resp *types.PushResp, err error) {
	// todo: add your logic here and delete this line

	return
}
