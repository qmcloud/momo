package router

import (
	v1 "leopardlive/api/v1"
	"leopardlive/middleware"

	"github.com/gin-gonic/gin"
)

func InitBaseRouter(Router *gin.RouterGroup) (R gin.IRoutes) {
	BaseRouter := Router.Group("base").Use(middleware.NeedInit())
	{
		BaseRouter.POST("login", v1.Login)
		BaseRouter.POST("captcha", v1.Captcha)
	}
	return BaseRouter
}
