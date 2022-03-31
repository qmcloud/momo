package main

import (
	"flag"
	"fmt"

	"backend/service/backend/cmd/rpc/systemuserget/internal/config"
	"backend/service/backend/cmd/rpc/systemuserget/internal/server"
	"backend/service/backend/cmd/rpc/systemuserget/internal/svc"
	"backend/service/backend/cmd/rpc/systemuserget/systemuserget"

	"github.com/zeromicro/go-zero/core/conf"
	"github.com/zeromicro/go-zero/zrpc"
	"google.golang.org/grpc"
)

var configFile = flag.String("f", "etc/systemuserget.yaml", "the config file")

func main() {
	flag.Parse()

	var c config.Config
	conf.MustLoad(*configFile, &c)
	ctx := svc.NewServiceContext(c)
	srv := server.NewSystemusergeterServer(ctx)

	s := zrpc.MustNewServer(c.RpcServerConf, func(grpcServer *grpc.Server) {
		systemuserget.RegisterSystemusergeterServer(grpcServer, srv)
	})
	defer s.Stop()

	fmt.Printf("Starting rpc server at %s...\n", c.ListenOn)
	s.Start()
}
