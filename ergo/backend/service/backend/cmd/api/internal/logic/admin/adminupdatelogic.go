package logic

import (
	"backend/common/errorx"
	"backend/common/utils"
	"context"
	"fmt"
	"github.com/jinzhu/copier"

	"backend/service/backend/cmd/api/internal/svc"
	"backend/service/backend/cmd/api/internal/types"

	"github.com/zeromicro/go-zero/core/logx"
)

type AdminUpdateLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

func NewAdminUpdateLogic(ctx context.Context, svcCtx *svc.ServiceContext) AdminUpdateLogic {
	return AdminUpdateLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *AdminUpdateLogic) AdminUpdate(req types.AdminPostReq) (*types.AdminReply, error) {
	// 检查重名
	duplicate, err2 := l.svcCtx.SystemUserModel.CheckUpdateDuplicate(req.UserName, req.Id)
	if duplicate.Id > 0 && err2 == nil {
		return nil, errorx.NewCodeError(201, fmt.Sprintf("用户名[%s]已经存在", req.UserName), "")
	}
	oldData, err := l.svcCtx.SystemUserModel.FindOne(req.Id)
	if err != nil {
		return nil, errorx.NewCodeError(201, fmt.Sprintf("%v", err), "")
	}
	oldPassword := oldData.Password
	// 密码留空，不修改
	if req.Password == "" {
		fmt.Println("--------------------oldData.Id0-----------------")
		fmt.Println(req.Password)
		fmt.Println(oldPassword)
		req.Password = oldPassword
	} else {
		req.Password = utils.MD5V(req.Password)
		fmt.Println("--------------------oldData.Id1-----------------")
		fmt.Println(req.Password)
	}
	copier.Copy(&oldData, &req)
	/*
		oldData.Id = req.Id
		oldData.UserName = req.UserName
		oldData.NickName = req.NickName
		oldData.UserType = req.UserType
		oldData.Email = req.Email
		oldData.Phonenumber = req.Phonenumber
		oldData.Avatar = req.Avatar
		oldData.LoginIp = req.LoginIp
		oldData.Remark = req.Remark
		oldData.DeptId = req.DeptId
		oldData.Sex = req.Sex
		oldData.DelFlag = req.DelFlag
		oldData.Password = req.Password
		oldData.Status = req.Status
		oldData.CreateBy = req.CreateBy
		oldData.UpdateBy = req.UpdateBy
	*/
	err = l.svcCtx.SystemUserModel.Update(*oldData)

	if err != nil {

		return nil, errorx.NewCodeError(201, fmt.Sprintf("%v", err), "")
	}

	return nil, errorx.NewCodeError(200, fmt.Sprintf("%s", "修改成功"), "")
}
