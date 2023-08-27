package logic

import (
	"context"

	"backend/service/im/cmd/rpc/internal/svc"
	"backend/service/im/cmd/rpc/pb"

	"github.com/zeromicro/go-zero/core/logx"
)

type PushSingleMsgLogic struct {
	ctx    context.Context
	svcCtx *svc.ServiceContext
	logx.Logger
}

func NewPushSingleMsgLogic(ctx context.Context, svcCtx *svc.ServiceContext) *PushSingleMsgLogic {
	return &PushSingleMsgLogic{
		ctx:    ctx,
		svcCtx: svcCtx,
		Logger: logx.WithContext(ctx),
	}
}

// 返送单条消息
func (l *PushSingleMsgLogic) PushSingleMsg(in *pb.PushRoomMsgRequest) (*pb.SuccessReply, error) {
	// todo: add your logic here and delete this line

	return &pb.SuccessReply{}, nil
}
