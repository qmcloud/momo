package logic

import (
	"context"

	"backend/service/im/cmd/rpc/internal/svc"
	"backend/service/im/cmd/rpc/pb"

	"github.com/zeromicro/go-zero/core/logx"
)

type PushSingleToConnectLogic struct {
	ctx    context.Context
	svcCtx *svc.ServiceContext
	logx.Logger
}

func NewPushSingleToConnectLogic(ctx context.Context, svcCtx *svc.ServiceContext) *PushSingleToConnectLogic {
	return &PushSingleToConnectLogic{
		ctx:    ctx,
		svcCtx: svcCtx,
		Logger: logx.WithContext(ctx),
	}
}

// 返送单条消息
func (l *PushSingleToConnectLogic) PushSingleToConnect(in *pb.TaskSend) (*pb.TaskSuccessReply, error) {
	// todo: add your logic here and delete this line

	return &pb.TaskSuccessReply{}, nil
}
