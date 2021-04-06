package egg

import (
	"encoding/json"
	"fmt"
	"net/http"
)

// 可以理解为 给map[string]interface{} 起别名H 构建JSON数据时，显得更简洁
type H map[string]interface{}

//Context目前只包含了http.ResponseWriter和*http.Request，另外提供了对 Method 和 Path 这两个常用属性的直接访问。
type Context struct {
	Write      http.ResponseWriter
	Req        *http.Request
	StatusCode int
	Path       string
	Method     string
	Params     map[string]string
}

//初始化新建contex上下文
func newContext(w http.ResponseWriter, r *http.Request) *Context {
	return &Context{
		Write:  w,
		Req:    r,
		Path:   r.URL.Path,
		Method: r.Method,
	}
}

//Param获取参数名
func (c *Context) Param(key string) string {
	Parmval, _ := c.Params[key]
	return Parmval
}

//封装PostForm接受post请求
func (c *Context) PostForm(key string) string {
	return c.Req.FormValue(key)
}

//封装Query获取get 参数
func (c *Context) Query(key string) string {
	return c.Req.URL.Query().Get(key)
}

//封装Status c.Write.StatusCode=code 记录code值
func (c *Context) Status(code int) {
	c.StatusCode = code
	c.Write.WriteHeader(code)
}

//设置header头信息
func (c *Context) SetHeader(key string, value string) {
	c.Write.Header().Set(key, value)
}

/****
EGG框架
提供了快速构造String/Data/JSON/HTML响应的方法。
*****/

//处理返回String数据
func (c *Context) String(code int, formate string, value ...interface{}) {
	c.SetHeader("Content-Type", "text/plain")
	c.Status(code)
	c.Write.Write([]byte(fmt.Sprintf(formate, value...)))
}

//处理返回Json
func (c *Context) Json(code int, obj interface{}) {
	c.SetHeader("Content-Type", "application/json")
	c.Status(code)
	encoder := json.NewEncoder(c.Write)
	if err := encoder.Encode(obj); err != nil {
		http.Error(c.Write, err.Error(), 500)
	}

}

func (c *Context) Data(code int, data []byte) {
	c.Status(code)
	c.Write.Write(data)
}

func (c *Context) HTML(code int, html string) {
	c.SetHeader("Content-Type", "text/html")
	c.Status(code)
	c.Write.Write([]byte(html))
}
