package push

import (
	"net/http"

	"backend/service/im/cmd/api/internal/logic/push"
	"backend/service/im/cmd/api/internal/svc"
	"backend/service/im/cmd/api/internal/types"
	"github.com/zeromicro/go-zero/rest/httpx"
)

func PushHandler(svcCtx *svc.ServiceContext) http.HandlerFunc {
	return func(w http.ResponseWriter, r *http.Request) {
		var req types.FormPush
		if err := httpx.Parse(r, &req); err != nil {
			httpx.Error(w, err)
			return
		}
		l := push.NewPushLogic(r.Context(), svcCtx)
		resp, err := l.Push(&req)
		if err != nil {
			httpx.Error(w, err)
		} else {
			httpx.OkJson(w, resp)
		}
	}
}
