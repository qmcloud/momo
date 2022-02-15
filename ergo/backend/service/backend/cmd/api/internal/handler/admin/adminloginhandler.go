package handler

import (
	"backend/common/errorx"
	"backend/service/backend/cmd/api/internal/logic/admin"
	"backend/service/backend/cmd/api/internal/svc"
	"backend/service/backend/cmd/api/internal/types"
	"fmt"
	"github.com/zeromicro/go-zero/rest/httpx"
	"net/http"
)

//func AdminLoginHandler(ctx *svc.ServiceContext) http.HandlerFunc {
//	return func(w http.ResponseWriter, r *http.Request) {
//		var req types.AdminLoginReq
//		if err := httpx.Parse(r, &req); err != nil {
//			httpx.Error(w, err)
//			return
//		}
//
//		l := logic.NewAdminLoginLogic(r.Context(), ctx)
//		resp, err := l.AdminLogin(req)
//		if err != nil {
//			httpx.Error(w, err)
//		} else {
//			httpx.OkJson(w, resp)
//		}
//	}
//}
func AdminLoginHandler(ctx *svc.ServiceContext) http.HandlerFunc {
	return func(w http.ResponseWriter, r *http.Request) {
		//w.Header().Set("Access-Control-Allow-Origin", "*")
		var req types.AdminLoginReq
		if err := httpx.Parse(r, &req); err != nil {
			httpx.Error(w, err)
			return
		}

		l := logic.NewAdminLoginLogic(r.Context(), ctx)
		ip := r.Header.Get("X-FORWARDED-FOR")
		if ip == "" {
			ip = r.RemoteAddr
		}

		resp, err := l.AdminLogin(req, ip)
		if err != nil {
			//httpx.Error(w, err)
			returnCode := 500
			var data interface{}
			data = ""
			switch e := err.(type) {
			case *errorx.CodeError:
				returnCode = e.DataInfo().Code
				data = e.DataInfo().Data
			default:
				returnCode = 500
			}
			httpx.Error(w, errorx.NewCodeError(returnCode, fmt.Sprintf("%v", err), data))
		} else {
			httpx.OkJson(w, resp)
		}
	}
}
