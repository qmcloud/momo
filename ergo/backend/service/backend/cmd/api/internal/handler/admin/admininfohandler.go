package handler

import (
	"backend/common/errorx"
	"backend/service/backend/cmd/api/internal/logic/admin"
	"backend/service/backend/cmd/api/internal/svc"
	"fmt"
	"github.com/zeromicro/go-zero/rest/httpx"
	"net/http"
)

func AdminInfoHandler(ctx *svc.ServiceContext) http.HandlerFunc {
	return func(w http.ResponseWriter, r *http.Request) {
		fmt.Println("-------AdminInfoHandlers-------")
		l := logic.NewAdminInfoLogic(r.Context(), ctx)
		resp, err := l.AdminInfo()
		if err != nil {
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
