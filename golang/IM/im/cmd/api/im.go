package main

import (
	"backend/service/im/cmd/api/internal/config"
	"backend/service/im/cmd/api/internal/handler"
	"backend/service/im/cmd/api/internal/imsvc"
	"backend/service/im/cmd/api/internal/svc"
	"flag"
	"fmt"
	"github.com/zeromicro/go-zero/core/conf"
	"github.com/zeromicro/go-zero/core/service"
	"github.com/zeromicro/go-zero/rest"
)

var configFile = flag.String("f", "etc/im.yaml", "the config file")

func main() {
	flag.Parse()
	//logx.Disable()
	var c config.Config
	conf.MustLoad(*configFile, &c)

	ctx := svc.NewServiceContext(c)
	fmt.Println("ServerId:", ctx.ServerId)
	server := rest.MustNewServer(c.RestConf, rest.WithCors())
	defer server.Stop()

	handler.RegisterHandlers(server, ctx)
	fmt.Printf("Starting Http at %s:%d...\n", c.Host, c.Port)

	group := service.NewServiceGroup()
	defer group.Stop()
	group.Add(server)
	group.Add(imsvc.Server{SvcCtx: ctx})
	group.Start()
}
