package logic

import (
	"backend/common/errorx"
	"backend/common/utils"
	"backend/service/backend/cmd/api/internal/svc"
	"backend/service/backend/cmd/api/internal/types"
	"context"
	"encoding/json"
	"fmt"
	"github.com/zeromicro/go-zero/core/logx"
	"io"
	"mime/multipart"
	"os"
	"path"
	"strings"
	"time"
)

type ChangeAvatarLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

func NewChangeAvatarLogic(ctx context.Context, svcCtx *svc.ServiceContext) ChangeAvatarLogic {
	return ChangeAvatarLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *ChangeAvatarLogic) ChangeAvatar(req types.AdminChangeAvatarReq, file *multipart.FileHeader) (*types.AdminReply, error) {
	dst := ""
	if file != nil {
		// 读取文件后缀
		ext := path.Ext(file.Filename)
		ext = strings.ToLower(ext)
		fmt.Println("------file-ext..", ext)
		// 读取文件名并加密
		fileName := strings.TrimSuffix(file.Filename, ext)
		fileName = utils.MD5V(fileName)
		// 拼接新文件名
		lastName := fileName + "_" + time.Now().Format("20060102150405") + ext
		fmt.Println("------filename..", fileName)
		fmt.Println("------newname..", lastName)
		// todo:读取全局变量的定义路径
		savePath := "uploads"
		// 尝试创建此路径
		err := os.MkdirAll(savePath, os.ModePerm)
		if err != nil {
			return nil, errorx.NewCodeError(202, fmt.Sprintf("%v", err), "")
		}
		// 拼接路径和文件名
		dst = savePath + "/" + lastName

		// 上传逻辑 begin
		// 打开文件 defer 关闭
		src, err := file.Open()
		if err != nil {
			return nil, errorx.NewCodeError(202, fmt.Sprintf("%v", err), "")
		}
		defer src.Close()
		// 创建文件 defer 关闭
		out, err := os.Create(dst)
		if err != nil {
			return nil, errorx.NewCodeError(202, fmt.Sprintf("%v", err), "")
		}
		defer out.Close()
		// 传输（拷贝）文件
		_, err = io.Copy(out, src)
		if err != nil {
			return nil, errorx.NewCodeError(202, fmt.Sprintf("%v", err), "")
		}
		// 上传逻辑 end

		// begin transaction
		// update avatar begin
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
		oldAvatarPath := savePath + "/" + oldUser.Avatar
		// 执行修改,只存储文件名
		oldUser.Avatar = lastName
		fmt.Printf("%v\n", oldUser)
		err = l.svcCtx.SystemUserModel.Update(*oldUser)
		if err != nil {
			return nil, errorx.NewCodeError(203, fmt.Sprintf("[%v]修改失败，请重试", err), "")
		}
		// update avatar end

		// delete old avatar begin
		if _, err := os.Stat(oldAvatarPath); os.IsNotExist(err) {
			// nofile
		} else {
			if err := os.Remove(oldAvatarPath); err != nil {
				fmt.Println("删除文件error")
			}
		}
		// delete old avatar end

	}
	return nil, errorx.NewCodeError(200, fmt.Sprintf("%v", "上传完成"), "/"+dst)

}
