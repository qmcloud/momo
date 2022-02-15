package handler

import (
	"net/http"

	"backend/service/backend/cmd/api/internal/logic/menu"
	"backend/service/backend/cmd/api/internal/svc"
	"backend/service/backend/cmd/api/internal/types"

	"github.com/zeromicro/go-zero/rest/httpx"
)

func TreeListHandler(ctx *svc.ServiceContext) http.HandlerFunc {
	return func(w http.ResponseWriter, r *http.Request) {
		var req types.MenuDelBatchReq
		if err := httpx.Parse(r, &req); err != nil {
			httpx.Error(w, err)
			return
		}

		l := logic.NewTreeListLogic(r.Context(), ctx)
		resp, err := l.TreeList(req)
		if err != nil {
			httpx.Error(w, err)
		} else {
			httpx.OkJson(w, resp)
		}
	}
}
