package config

import (
	"github.com/zeromicro/go-zero/core/stores/cache"
	"github.com/zeromicro/go-zero/core/stores/redis"
	"github.com/zeromicro/go-zero/rest"
	"github.com/zeromicro/go-zero/zrpc"
)

type Config struct {
	rest.RestConf
	Redis   redis.RedisConf
	JwtAuth struct {
		AccessSecret string
		AccessExpire int64
	}
	Mysql struct {
		DataSource string
	}
	WSPort        int
	WSHander      string
	CacheRedis    cache.CacheConf
	WsRpcConf     zrpc.RpcClientConf
	ConnectBucket struct {
		CpuNum        int
		Channel       int
		Room          int
		SvrProto      int
		RoutineAmount uint64
		RoutineSize   int
	}
}
