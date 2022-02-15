package handler

import (
	"net/http"

	"backend/service/backend/cmd/api/internal/logic/admin"
	"backend/service/backend/cmd/api/internal/svc"
	"github.com/zeromicro/go-zero/rest/httpx"
)

func AdminInitHandler(ctx *svc.ServiceContext) http.HandlerFunc {
	return func(w http.ResponseWriter, r *http.Request) {

		l := logic.NewAdminInitLogic(r.Context(), ctx)
		resp, err := l.AdminInit()
		if err != nil {
			httpx.Error(w, err)
		} else {
			httpx.OkJson(w, resp)
		}
	}
}
