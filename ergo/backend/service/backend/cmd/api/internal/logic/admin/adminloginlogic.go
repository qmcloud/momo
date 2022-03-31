package logic

import (
	"backend/common/errorx"
	"backend/common/utils"
	"backend/service/backend/cmd/rpc/systemuserget/systemusergeter"
	"backend/service/backend/model"
	"context"
	"encoding/json"
	"fmt"
	"github.com/dgrijalva/jwt-go"
	"strings"
	"time"

	"backend/service/backend/cmd/api/internal/svc"
	"backend/service/backend/cmd/api/internal/types"

	"github.com/zeromicro/go-zero/core/logx"
)

type AdminLoginLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

func NewAdminLoginLogic(ctx context.Context, svcCtx *svc.ServiceContext) AdminLoginLogic {
	return AdminLoginLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *AdminLoginLogic) AdminLogin(req types.AdminLoginReq, ip string) (*types.AdminReply, error) {
	if len(strings.TrimSpace(req.Username)) == 0 || len(strings.TrimSpace(req.Password)) == 0 {
		return nil, errorx.NewDefaultError("参数错误", "")
	}
	userInfo, err := l.svcCtx.SystemUserModel.FindOneByUserName(req.Username)
	switch err {
	case nil:
	case model.ErrNotFound:
		return nil, errorx.NewDefaultError("用户名密码不正确", "")
		//return nil, errorx.NewDefaultError("用户名不存在", "")
	default:
		return nil, err
	}

	if userInfo.Password != utils.MD5V(req.Password) {
		//fmt.Println("req.password", utils.MD5V(req.Password))
		//fmt.Println("req.Username", req.Username)
		//fmt.Printf("userInfo.Password[%s]\n", userInfo.Password)
		//fmt.Printf("userInfo.Name[%s]\n", userInfo.Name)
		//fmt.Printf("userInfo:%v\n", userInfo)
		return nil, errorx.NewDefaultError("用户名或密码不正确", "")
	}

	// ---start---
	now := time.Now().Unix()
	accessExpire := l.svcCtx.Config.Auth.AccessExpire
	fmt.Println(accessExpire)
	jwtToken, err := l.getAdminJwtToken(l.svcCtx.Config.Auth.AccessSecret, now, l.svcCtx.Config.Auth.AccessExpire, userInfo)
	if err != nil {
		return nil, errorx.NewCodeError(201, fmt.Sprintf("[授权错误]%v", err), "")
	}
	// ---end---
	tokenInfo := make(map[string]interface{})
	tokenInfo["token"] = jwtToken
	//tokenInfo["menus"] = l.getAdminsMenu(userInfo.RoleId)

	//tokenInfo["roles"] = []string{"admin", "admin1"}
	user := make(map[string]interface{})
	//user["nickName"] = userInfo.NickName
	user["headerImg"] = "/uploads/" + userInfo.Avatar //"https://wpimg.wallstcn.com/9e2a5d0a-bd5b-457f-ac8e-86554616c87b.jpg"
	authority := make(map[string]interface{})
	// menurouter-name
	authority["defaultRouter"] = "dashboard"
	user["authority"] = authority

	// 验证rpc
	systemuserResp, err := l.svcCtx.Systemusergeter.GetSystemuser(l.ctx, &systemusergeter.Request{
		Id: userInfo.Id,
	})
	if err != nil {
		fmt.Println("rpc----error", err)
	}
	//通过rpc获取nickname
	user["nickName"] = systemuserResp.NickName

	tokenInfo["user"] = user

	//更新登录信息
	userInfo.LoginDate = time.Now()
	userInfo.LoginIp = ip
	err = l.svcCtx.SystemUserModel.Update(userInfo)
	if err != nil {
		return nil, errorx.NewCodeError(202, fmt.Sprintf("[数据处理错误]%v", err), "")
	}
	return nil, errorx.NewCodeError(200, "登录成功", tokenInfo)
}

// get admin's menu
func (l *AdminLoginLogic) getAdminsMenu(roleId int64) interface{} {
	list, err := l.svcCtx.SystemRoleMenusModel.ListByRoleId(roleId)
	if err != nil {
		return nil
	}
	menuIds := ""
	for _, v := range list {
		if menuIds == "" {
			menuIds += fmt.Sprintf("%v", v.MenuId)
		} else {
			menuIds += fmt.Sprintf(",%v", v.MenuId)
		}
	}
	// 防止查询所有
	if menuIds == "" {
		menuIds = "0"
	}
	adminsMenu, _ := l.svcCtx.SystemMenusModel.Tree(menuIds)

	// 构造菜单树
	var topMenu []interface{}
	for _, v := range adminsMenu {
		// 一级
		if v.ParentId == 0 {
			// 结构体转json
			sJson, err := json.Marshal(v)
			if err != nil {
				fmt.Println(err)
			}
			// json 转map
			m := make(map[string]interface{})
			json.Unmarshal(sJson, &m)
			meta := make(map[string]interface{})
			meta["title"] = v.Title
			delete(m, "title")
			meta["icon"] = v.Icon
			delete(m, "icon")
			m["meta"] = meta

			delete(m, "created_at")
			delete(m, "updated_at")
			delete(m, "deleted_at")

			delete(m, "id")
			delete(m, "parent_id")
			delete(m, "sort")

			// 二级
			//var children []interface{}
			children := make([]interface{}, 0) //空切片
			for _, subv := range adminsMenu {
				if subv.ParentId > 0 && v.Id == subv.ParentId {
					// 结构体转json
					subJson, err := json.Marshal(subv)
					if err != nil {
						fmt.Println(err)
					}
					// json 转map
					subMenu := make(map[string]interface{})
					json.Unmarshal(subJson, &subMenu)
					childMeta := make(map[string]interface{})
					childMeta["title"] = v.Title
					delete(subMenu, "title")
					childMeta["icon"] = v.Icon
					delete(subMenu, "icon")
					subMenu["meta"] = childMeta

					delete(subMenu, "created_at")
					delete(subMenu, "updated_at")
					delete(subMenu, "deleted_at")

					delete(subMenu, "id")
					delete(subMenu, "parent_id")
					delete(subMenu, "sort")

					children = append(children, subMenu)
				}
			}
			m["children"] = children
			topMenu = append(topMenu, m)
		}
	}

	return topMenu
}
func (l *AdminLoginLogic) getAdminJwtToken(secretKey string, iat, seconds int64, userInfo model.SystemUser) (string, error) {
	claims := make(jwt.MapClaims)
	claims["exp"] = iat + seconds
	claims["iat"] = iat
	claims["userId"] = userInfo.Id
	claims["roleId"] = userInfo.RoleId
	token := jwt.New(jwt.SigningMethodHS256)
	token.Claims = claims
	return token.SignedString([]byte(secretKey))
}
