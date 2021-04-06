package egg

import (
	"net/http"
	"strings"
)

// @title 路由
// @Description
// @Author egg
// @Update  2021 4.1

/*
router 的 handle 方法作了一个细微的调整，即 handler 的参数，变成了 Context
Trie 树的插入与查找 Trie 树应用到路由中。我们使用 roots 来存储每种请求方式的Trie 树根节点
*/

type router struct {
	roots    map[string]*node       //利用roots记录 trie树的结点
	handlers map[string]HandlerFunc //对应的HandlerFunc
}

func newRouter() *router {
	return &router{
		roots:    make(map[string]*node),
		handlers: make(map[string]HandlerFunc),
	}
}

//parsePattern 处理pattern 以"/"拆分路由pattern 并返回parts 数组
func parsePatterns(pattern string) []string {
	val := strings.Split(pattern, "/")
	parts := make([]string, 0)
	for _, item := range val {
		if item != "" {
			parts = append(parts, item)
			if item[0] == '*' {
				break
			}
		}
	}
	return parts
}

//addRoute 绑定路由 key := method + "-" + pattern
func (r *router) addRoute(method string, pattern string, handler HandlerFunc) {
	parts := parsePatterns(pattern)
	key := method + "-" + pattern
	_, ok := r.roots[method]
	if !ok {
		r.roots[method] = &node{}
	}
	r.roots[method].insert(pattern, parts, 0)
	r.handlers[key] = handler

}

//getRoute 获取route 路由 返回parms用map存储 如路由/:name {name:"xxxx"}
func (r *router) getRoute(method string, part string) (*node, map[string]string) {
	searchParts := parsePatterns(part)
	params := make(map[string]string)
	root, ok := r.roots[method]
	if !ok {
		return nil, nil
	}
	node := root.search(searchParts, 0)
	if node != nil {
		parts := parsePatterns(node.pattern)
		for index, part := range parts {
			if part[0] == ':' {
				params[part[1:]] = searchParts[index]
			}
			if part[0] == '*' && len(part) > 1 {
				params[part[1:]] = strings.Join(searchParts[index:], "/")
				break
			}
		}
		return node, params
	}

	return nil, nil

}

//handle 通过contex 参数作为上下文传递
func (r *router) handle(c *Context) {
	n, params := r.getRoute(c.Method, c.Path)

	if n != nil {
		key := c.Method + "-" + n.pattern
		c.Params = params
		c.handlers = append(c.handlers, r.handlers[key])
	} else {
		c.handlers = append(c.handlers, func(c *Context) {
			c.String(http.StatusNotFound, "404 NOT FOUND: %s\n", c.Path)
		})
	}
	c.Next()
}
