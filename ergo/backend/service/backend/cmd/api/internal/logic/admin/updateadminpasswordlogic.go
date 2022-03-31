package logic

import (
	"backend/common/errorx"
	"backend/common/utils"
	"context"
	"encoding/json"
	"fmt"
	"strings"

	"backend/service/backend/cmd/api/internal/svc"
	"backend/service/backend/cmd/api/internal/types"

	"github.com/zeromicro/go-zero/core/logx"
)

type UpdateadminpasswordLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

func NewUpdateadminpasswordLogic(ctx context.Context, svcCtx *svc.ServiceContext) UpdateadminpasswordLogic {
	return UpdateadminpasswordLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *UpdateadminpasswordLogic) Updateadminpassword(req types.AdminUpdatePwdReq) (*types.AdminReply, error) {
	if len(strings.TrimSpace(req.Password)) == 0 || len(strings.TrimSpace(req.OldPassword)) == 0 {
		return nil, errorx.NewDefaultError("参数错误", "")
	}

	userIdNumber := json.Number(fmt.Sprintf("%v", l.ctx.Value("userId")))
	userId, err := userIdNumber.Int64()
	if err != nil {
		return nil, errorx.NewCodeError(401, "请重新登录再操作", "")
	}
	// 获取用户信息
	oldUser, err := l.svcCtx.SystemUserModel.FindOne(userId)
	if err != nil {
		return nil, errorx.NewCodeError(201, "数据错误", "")
	}
	//验证原密码
	if oldUser.Password != utils.MD5V(req.OldPassword) {
		return nil, errorx.NewCodeError(202, "原密码不正确", "")
	}
	// 执行修改
	oldUser.Password = utils.MD5V(req.Password)
	fmt.Printf("%v\n", oldUser)
	err = l.svcCtx.SystemUserModel.Update(*oldUser)
	if err != nil {
		return nil, errorx.NewCodeError(203, fmt.Sprintf("[%v]修改失败，请重试", err), "")
	}

	return nil, errorx.NewCodeError(200, "修改成功", "")
}
