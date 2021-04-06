package egg

import (
	"log"
	"net/http"
	"strings"
)

//定义 HandlerFunc 抽象 通过*Context 上下文传递
type HandlerFunc func(*Context)

//定义调用
type Engine struct {
	*RouterGroup
	router *router
	groups []*RouterGroup //储存所有的groups
}

//定义group分组
type RouterGroup struct {
	prefix      string        //前缀
	parent      *RouterGroup  //分组
	middlewares []HandlerFunc // 支持 middleware
	engine      *Engine       //ALL router 支持引擎

}

//定义初始化
func New() *Engine {
	engine := &Engine{router: newRouter()}
	engine.RouterGroup = &RouterGroup{engine: engine}
	engine.groups = []*RouterGroup{engine.RouterGroup}
	return engine
}

//group
func (group *RouterGroup) Group(prefix string) *RouterGroup {
	engine := group.engine
	newGroup := &RouterGroup{
		prefix: group.prefix + prefix,
		parent: group,
		engine: engine,
	}
	engine.groups = append(engine.groups, newGroup)
	return newGroup
}

//addRoute
func (group *RouterGroup) addRoute(method string, pattern string, handle HandlerFunc) {
	patterns := group.prefix + pattern
	log.Printf("Route %4s - %s", method, patterns)
	group.engine.router.addRoute(method, patterns, handle)
}

//get
func (group *RouterGroup) Get(pattern string, handle HandlerFunc) {
	group.addRoute("GET", pattern, handle)
}

//post
func (group *RouterGroup) Post(pattern string, handle HandlerFunc) {
	group.addRoute("POST", pattern, handle)
}

// Use is defined to add middleware to the group
func (group *RouterGroup) Use(middlewares ...HandlerFunc) {
	group.middlewares = append(group.middlewares, middlewares...)
}

func (e *Engine) ServeHTTP(w http.ResponseWriter, r *http.Request) {
	var middlewares []HandlerFunc
	for _, group := range e.groups {
		if strings.HasPrefix(r.URL.Path, group.prefix) {
			middlewares = append(middlewares, group.middlewares...)
		}
	}
	c := newContext(w, r)
	c.handlers = middlewares
	e.router.handle(c)
}

//run
func (e *Engine) Run(addr string) (err error) {
	return http.ListenAndServe(addr, e)
}
