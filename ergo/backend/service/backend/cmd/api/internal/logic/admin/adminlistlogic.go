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
)

type AdminListLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

func NewAdminListLogic(ctx context.Context, svcCtx *svc.ServiceContext) AdminListLogic {
	return AdminListLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *AdminListLogic) AdminList(req types.AdminListReq) (*types.AdminReply, error) {
	reqParam := utils.ListReq{}
	reqParam.Page = req.Page
	reqParam.PageSize = req.PageSize
	reqParam.Keyword = req.Keyword
	list, i, err := l.svcCtx.SystemUserModel.List(reqParam)
	if err != nil {
		return nil, errorx.NewCodeError(201, fmt.Sprintf("%v", err), "")
	}
	data := make(map[string]interface{})
	data["page"] = req.Page
	data["pageSize"] = req.PageSize
	data["total"] = i
	//data["list"] = list

	// 获取所有部门 todo: 后续放redis
	departmentsMap := make(map[int64]interface{})
	departments, _, _ := l.svcCtx.SystemDepartmentsModel.List(utils.ListReq{
		Page:     1,
		PageSize: 200,
	})
	for _, v := range departments {
		departmentsMap[v.Id] = v.Name
	}

	// 获取所有角色 todo: 后续放redis
	rolesMap := make(map[int64]interface{})
	roles, _, _ := l.svcCtx.SystemRolesModel.List(utils.ListReq{
		Page:     1,
		PageSize: 200,
	})
	for _, v := range roles {
		rolesMap[v.Id] = v.Name
	}

	// todo:
	var resultMap []interface{}
	// 获取部门名称
	// 获取角色名
	for _, v := range list {

		// 结构体转json
		sJson, err := json.Marshal(v)
		if err != nil {
			fmt.Println(err)
		}

		// json 转map
		m := make(map[string]interface{})
		json.Unmarshal(sJson, &m)

		// 补充字段
		m["department_name"] = ""
		if departmentName, ok := departmentsMap[v.DeptId]; ok {
			m["department_name"] = departmentName
		}

		m["role_name"] = ""
		if roleName, ok := rolesMap[v.RoleId]; ok {
			m["role_name"] = roleName
		}

		resultMap = append(resultMap, m)
	}
	data["list"] = resultMap

	return nil, errorx.NewCodeError(200, "ok", data)
}
