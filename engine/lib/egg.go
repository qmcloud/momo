package egg

import (
	"log"
	"net/http"
)

//定义 HandlerFunc 抽象 通过*Context 上下文传递
type HandlerFunc func(*Context)

//定义调用
type Engine struct {
	router *router
}

//定义初始化
func New() *Engine {
	return &Engine{router: newRouter()}
}

//addRoute
func (e *Engine) addRoute(method string, pattern string, handle HandlerFunc) {
	log.Printf("Route %4s - %s", method, pattern)
	e.router.addRoute(method, pattern, handle)
}

//get
func (e *Engine) Get(pattern string, handle HandlerFunc) {
	e.addRoute("GET", pattern, handle)
}

//post
func (e *Engine) Post(pattern string, handle HandlerFunc) {
	e.addRoute("POST", pattern, handle)
}

//run
func (e *Engine) Run(addr string) (err error) {
	return http.ListenAndServe(addr, e)
}

func (e *Engine) ServeHTTP(w http.ResponseWriter, r *http.Request) {
	c := newContext(w, r)
	e.router.handle(c)
}
