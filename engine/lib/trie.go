package egg

import (
	"fmt"
	"strings"
)

//使用 Trie 树实现动态路由(dynamic route)解析。
//支持两种模式:name和*filepath，代码约150行。
type node struct {
	pattern  string  //全路径
	part     string  //部分路径
	children []*node //子节点
	isMatch  bool    //是否模糊匹配
}

//字符串格式化
func (n *node) String() string {
	return fmt.Sprintf("node{pattern=%s,part=%s,isMatch=%t}", n.pattern, n.part, n.isMatch)
}

//insert 插入子节点
func (n *node) insert(pattern string, parts []string, height int) {
	if len(parts) == height {
		n.pattern = pattern
		return
	}
	part := parts[height]
	child := n.matchChild(part)
	if child == nil {
		child = &node{part: part, isMatch: part[0] == ':' || part[0] == '*'}
		n.children = append(n.children, child)
	}
	child.insert(pattern, parts, height+1)
}

//search 查找子节点数据
func (n *node) search(parts []string, height int) *node {
	if len(parts) == height || strings.HasPrefix(n.part, "*") {
		if n.pattern == "" {
			return nil
		}
		return n
	}
	part := parts[height]
	children := n.matchChildren(part)

	for _, childs := range children {
		res := childs.search(parts, height+1)
		if res != nil {
			return res
		}
	}

	return nil

}

//matchChild 寻找字节点 第一个匹配成功的节点，用于插入
func (n *node) matchChild(part string) *node {
	for _, child := range n.children {
		if child.part == part || child.isMatch {
			return child
		}
	}
	return nil
}

func (n *node) matchChildren(part string) []*node {
	nodes := make([]*node, 0)
	for _, child := range n.children {
		if child.part == part || child.isMatch {
			nodes = append(nodes, child)
		}
	}
	return nodes
}
