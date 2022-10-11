package logic

import (
	"context"
	"fmt"

	"backend/service/im/cmd/rpc/internal/svc"
	"backend/service/im/cmd/rpc/pb"

	"github.com/zeromicro/go-zero/core/logx"
)

type ConnectLogic struct {
	ctx    context.Context
	svcCtx *svc.ServiceContext
	logx.Logger
}

func NewConnectLogic(ctx context.Context, svcCtx *svc.ServiceContext) *ConnectLogic {
	return &ConnectLogic{
		ctx:    ctx,
		svcCtx: svcCtx,
		Logger: logx.WithContext(ctx),
	}
}

func (l *ConnectLogic) Connect(in *pb.ConnectRequest) (*pb.ConnectReply, error) {
	// todo: add your logic here and delete this line
	fmt.Println("call rpc conn test ~~~ service.id = ", in.ServerId)
	return &pb.ConnectReply{}, nil
}
