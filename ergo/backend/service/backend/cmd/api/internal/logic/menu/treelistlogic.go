package logic

import (
	"backend/common/errorx"
	"backend/service/backend/cmd/api/internal/svc"
	"backend/service/backend/cmd/api/internal/types"
	"backend/service/backend/model"
	"context"
	"encoding/json"
	"fmt"
	"github.com/zeromicro/go-zero/core/logx"
)

type TreeListLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

func NewTreeListLogic(ctx context.Context, svcCtx *svc.ServiceContext) TreeListLogic {
	return TreeListLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *TreeListLogic) TreeList(req types.MenuDelBatchReq) (*types.MenuReply, error) {

	list, err := l.svcCtx.SystemMenusModel.Tree(req.Ids)
	if err != nil {
		return nil, errorx.NewCodeError(201, fmt.Sprintf("%v", err), "")
	}
	data := make(map[string]interface{})
	//data["list"] = list

	var topMenu []interface{}
	for _, v := range list {
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

			// 二级
			children := make([]model.SystemMenus, 0)
			for _, subv := range list {
				if subv.ParentId > 0 && v.Id == subv.ParentId {
					children = append(children, *subv)
				}
			}

			m["children"] = children
			topMenu = append(topMenu, m)
		}
	}
	data["list"] = topMenu
	return nil, errorx.NewCodeError(200, "ok", data)
}
