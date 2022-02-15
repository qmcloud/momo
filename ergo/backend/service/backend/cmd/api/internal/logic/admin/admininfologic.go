package logic

import (
	"backend/common/errorx"
	"backend/service/backend/cmd/api/internal/svc"
	"backend/service/backend/cmd/api/internal/types"
	"context"
	"encoding/json"
	"fmt"

	"github.com/zeromicro/go-zero/core/logx"
)

type AdminInfoLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

func NewAdminInfoLogic(ctx context.Context, svcCtx *svc.ServiceContext) AdminInfoLogic {
	return AdminInfoLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *AdminInfoLogic) AdminInfo() (*types.AdminReply, error) {
	data := make(map[string]interface{})
	//roles,name, avatar, introduction
	//data["roles"] = []string{"admin", "admin1"}
	//data["name"] = "qinrq"
	//data["avatar"] = "https://wpimg.wallstcn.com/9e2a5d0a-bd5b-457f-ac8e-86554616c87b.jpg"
	//data["introduction"] = "devops"
	roleIdNumber := json.Number(fmt.Sprintf("%v", l.ctx.Value("roleId")))
	roleId, _ := roleIdNumber.Int64()
	data["menus"] = l.getAdminsMenu(roleId)
	return nil, errorx.NewCodeError(200, "success", data)
}

// get admin's menu
func (l *AdminInfoLogic) getAdminsMenu(roleId int64) interface{} {
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
			var children []interface{}
			//children := make([]interface{}, 0) //空切片
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

					subMeta := make(map[string]interface{})
					subMeta["title"] = subv.Title
					subMeta["icon"] = subv.Icon
					subMenu["meta"] = subMeta

					delete(subMenu, "title")
					delete(subMenu, "icon")
					delete(subMenu, "created_at")
					delete(subMenu, "updated_at")
					delete(subMenu, "deleted_at")

					delete(subMenu, "id")
					delete(subMenu, "parent_id")
					delete(subMenu, "sort")

					children = append(children, subMenu)
				}
			}
			fmt.Println("================children-len=========", len(children))
			m["children"] = children
			topMenu = append(topMenu, m)
		}
	}

	return topMenu
}
func (l *AdminInfoLogic) getAdminsMenu1(roleId int64) interface{} {
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
			i := 0
			firstPath := ""
			for _, subv := range adminsMenu {
				if subv.ParentId > 0 && v.Id == subv.ParentId {
					if i == 0 {
						firstPath = subv.Path
						i++
					}
					// 结构体转json
					subJson, err := json.Marshal(subv)
					if err != nil {
						fmt.Println(err)
					}
					// json 转map
					subMenu := make(map[string]interface{})
					json.Unmarshal(subJson, &subMenu)

					subMeta := make(map[string]interface{})
					subMeta["title"] = v.Title
					subMeta["icon"] = v.Icon
					subMenu["meta"] = subMeta

					delete(subMenu, "title")
					delete(subMenu, "icon")
					delete(subMenu, "created_at")
					delete(subMenu, "updated_at")
					delete(subMenu, "deleted_at")

					delete(subMenu, "id")
					delete(subMenu, "parent_id")
					delete(subMenu, "sort")

					children = append(children, subMenu)
				}
			}

			// 一级，补充一个children
			if len(children) == 0 {
				fmt.Println("================children=========", v.Name)
				subMenu := make(map[string]interface{})

				subMenu["name"] = v.Name
				subMenu["path"] = v.Path
				subMenu["component"] = v.Component

				subMeta := make(map[string]interface{})
				subMeta["title"] = v.Title
				subMeta["icon"] = v.Icon
				subMenu["meta"] = subMeta
				children = append(children, subMenu)
				//{
				//	path: 'index',
				//	component: () => import('@/views/icons/index'),
				//	name: 'Icons',
				//	meta: { title: 'Icons', icon: 'icon', noCache: true }
				//}

				// 处理一级redirect
				//delete(m, "meta")
				delete(m, "hidden")
				//delete(m, "name")
				m["redirect"] = "/" + v.Path + "/" + v.Path
				m["component"] = "/layout/index"
				m["path"] = "/" + v.Path
			} else {
				//处理一级，redirect
				m["redirect"] = "/" + v.Path + "/" + firstPath
				m["component"] = "/layout/index"
				m["path"] = "/" + v.Path
			}
			fmt.Println("================children-len=========", len(children))
			m["children"] = children
			topMenu = append(topMenu, m)
		}
	}

	return topMenu
}
