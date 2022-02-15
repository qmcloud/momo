package main

import (
	"backend/common/errorx"
	"backend/service/backend/cmd/api/internal/config"
	"backend/service/backend/cmd/api/internal/handler"
	"backend/service/backend/cmd/api/internal/svc"
	"flag"
	"fmt"
	"github.com/zeromicro/go-zero/core/conf"
	"github.com/zeromicro/go-zero/rest"
	"github.com/zeromicro/go-zero/rest/httpx"
	"net/http"
)

var configFile = flag.String("f", "etc/admin-api.yaml", "the config file")

func main() {
	flag.Parse()

	var c config.Config
	conf.MustLoad(*configFile, &c)

	ctx := svc.NewServiceContext(c)
	server := rest.MustNewServer(c.RestConf, rest.WithUnauthorizedCallback(func(w http.ResponseWriter, r *http.Request, err error) {
		//w.Header().Set("Access-Control-Allow-Origin", "*")
		fmt.Println("===========jwt=WithUnauthorizedCallback=====================")
		httpx.Error(w, errorx.NewCodeError(401, fmt.Sprintf("%v", err), ""))
	}))
	defer server.Stop()

	// 全局中间件
	server.Use(func(next http.HandlerFunc) http.HandlerFunc {
		return func(w http.ResponseWriter, r *http.Request) {
			ip := r.Header.Get("X-FORWARDED-FOR")
			fmt.Println("ip1>>>", ip)
			if ip == "" {
				ip = r.RemoteAddr
			}
			fmt.Println("ip2>>>", ip)
			fmt.Println("===========>>>>>>>>>>>>>>>>>>>>>.global middleware", ip)

			next(w, r)
		}
	})

	handler.RegisterHandlers(server, ctx)

	// 自定义错误
	httpx.SetErrorHandler(func(err error) (int, interface{}) {

		switch e := err.(type) {
		case *errorx.CodeError:
			return http.StatusOK, e.DataInfo()
		default:
			return http.StatusInternalServerError, nil
		}
	})

	fmt.Printf("Starting server at %s:%d...\n", c.Host, c.Port)
	server.Start()
}
