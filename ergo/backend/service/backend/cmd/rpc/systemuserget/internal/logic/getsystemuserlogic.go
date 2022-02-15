package logic

import (
	"context"
	"fmt"

	"backend/service/backend/cmd/rpc/systemuserget/internal/svc"
	"backend/service/backend/cmd/rpc/systemuserget/systemuserget"

	"github.com/zeromicro/go-zero/core/logx"
)

type GetSystemuserLogic struct {
	ctx    context.Context
	svcCtx *svc.ServiceContext
	logx.Logger
}

func NewGetSystemuserLogic(ctx context.Context, svcCtx *svc.ServiceContext) *GetSystemuserLogic {
	return &GetSystemuserLogic{
		ctx:    ctx,
		svcCtx: svcCtx,
		Logger: logx.WithContext(ctx),
	}
}

func (l *GetSystemuserLogic) GetSystemuser(in *systemuserget.Request) (*systemuserget.Response, error) {
	fmt.Println("GetSystemuser-Id:", in.Id)
	one, err := l.svcCtx.SystemUserModel.FindOne(in.Id)
	if err != nil {
		fmt.Println("-------------err--------rpc----GetSystemuser------")
		return nil, err
	}
	fmt.Println("GetSystemuser-nickname:", one.NickName)

	return &systemuserget.Response{
		NickName: one.NickName + ":rpc",
	}, nil
}
