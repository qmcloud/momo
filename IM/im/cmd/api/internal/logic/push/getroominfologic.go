package push

import (
	"context"

	"backend/service/im/cmd/api/internal/svc"
	"backend/service/im/cmd/api/internal/types"

	"github.com/zeromicro/go-zero/core/logx"
)

type GetroominfoLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

func NewGetroominfoLogic(ctx context.Context, svcCtx *svc.ServiceContext) *GetroominfoLogic {
	return &GetroominfoLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *GetroominfoLogic) Getroominfo(req *types.FormCount) (resp *types.PushResp, err error) {
	// todo: add your logic here and delete this line

	return
}
