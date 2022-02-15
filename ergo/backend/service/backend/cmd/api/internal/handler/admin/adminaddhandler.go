package handler

import (
	"backend/common/errorx"
	"fmt"
	"net/http"

	"backend/service/backend/cmd/api/internal/logic/admin"
	"backend/service/backend/cmd/api/internal/svc"
	"backend/service/backend/cmd/api/internal/types"

	"github.com/zeromicro/go-zero/rest/httpx"
)

func AdminAddHandler(ctx *svc.ServiceContext) http.HandlerFunc {
	//return func(w http.ResponseWriter, r *http.Request) {
	//	var req types.AdminPostReq
	//	if err := httpx.Parse(r, &req); err != nil {
	//		httpx.Error(w, err)
	//		return
	//	}
	//
	//	l := logic.NewAdminAddLogic(r.Context(), ctx)
	//	resp, err := l.AdminAdd(req)
	//	if err != nil {
	//		httpx.Error(w, err)
	//	} else {
	//		httpx.OkJson(w, resp)
	//	}
	//}
	return func(w http.ResponseWriter, r *http.Request) {
		var req types.AdminPostReq
		if err := httpx.Parse(r, &req); err != nil {
			httpx.Error(w, errorx.NewDefaultError(fmt.Sprintf("%v", err), ""))
			return
		}

		l := logic.NewAdminAddLogic(r.Context(), ctx)
		//ip := r.Header.Get("X-FORWARDED-FOR")
		resp, err := l.AdminAdd(req)
		if err != nil {
			returnCode := 500
			switch e := err.(type) {
			case *errorx.CodeError:
				returnCode = e.DataInfo().Code
			default:
				returnCode = 500
			}
			httpx.Error(w, errorx.NewCodeError(returnCode, fmt.Sprintf("%v", err), ""))
		} else {
			httpx.OkJson(w, resp)
		}
	}
}
