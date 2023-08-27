package logic

import (
	"context"

	"backend/service/im/cmd/rpc/internal/svc"
	"backend/service/im/cmd/rpc/pb"

	"github.com/zeromicro/go-zero/core/logx"
)

type PushLogic struct {
	ctx    context.Context
	svcCtx *svc.ServiceContext
	logx.Logger
}

func NewPushLogic(ctx context.Context, svcCtx *svc.ServiceContext) *PushLogic {
	return &PushLogic{
		ctx:    ctx,
		svcCtx: svcCtx,
		Logger: logx.WithContext(ctx),
	}
}

// 返送单条消息
func (l *PushLogic) Push(in *pb.Send) (*pb.LGSuccessReply, error) {
	// todo: add your logic here and delete this line

	return &pb.LGSuccessReply{}, nil
}
