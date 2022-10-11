package push

import (
	"context"

	"backend/service/im/cmd/api/internal/svc"
	"backend/service/im/cmd/api/internal/types"

	"github.com/zeromicro/go-zero/core/logx"
)

type PushroomLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

func NewPushroomLogic(ctx context.Context, svcCtx *svc.ServiceContext) *PushroomLogic {
	return &PushroomLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *PushroomLogic) Pushroom(req *types.FormRoom) (resp *types.PushResp, err error) {
	// todo: add your logic here and delete this line

	return
}
