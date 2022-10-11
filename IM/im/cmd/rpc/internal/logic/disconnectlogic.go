package logic

import (
	"context"
	"fmt"

	"backend/service/im/cmd/rpc/internal/svc"
	"backend/service/im/cmd/rpc/pb"

	"github.com/zeromicro/go-zero/core/logx"
)

type DisConnectLogic struct {
	ctx    context.Context
	svcCtx *svc.ServiceContext
	logx.Logger
}

func NewDisConnectLogic(ctx context.Context, svcCtx *svc.ServiceContext) *DisConnectLogic {
	return &DisConnectLogic{
		ctx:    ctx,
		svcCtx: svcCtx,
		Logger: logx.WithContext(ctx),
	}
}

func (l *DisConnectLogic) DisConnect(in *pb.DisConnectRequest) (*pb.DisConnectReply, error) {
	// todo: add your logic here and delete this line
	fmt.Println("call rpc dis conn test ~~~ service.id = ", in.UserId)
	return &pb.DisConnectReply{}, nil
}
