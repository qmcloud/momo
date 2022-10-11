package push

import (
	"net/http"

	"backend/service/im/cmd/api/internal/logic/push"
	"backend/service/im/cmd/api/internal/svc"
	"backend/service/im/cmd/api/internal/types"
	"github.com/zeromicro/go-zero/rest/httpx"
)

func GetroominfoHandler(svcCtx *svc.ServiceContext) http.HandlerFunc {
	return func(w http.ResponseWriter, r *http.Request) {
		var req types.FormCount
		if err := httpx.Parse(r, &req); err != nil {
			httpx.Error(w, err)
			return
		}

		l := push.NewGetroominfoLogic(r.Context(), svcCtx)
		resp, err := l.Getroominfo(&req)
		if err != nil {
			httpx.Error(w, err)
		} else {
			httpx.OkJson(w, resp)
		}
	}
}
