package main

import (
	"egg"
	"net/http"
)

func main() {
	var r = egg.New()

	r.Get("/hello", func(c *egg.Context) {
		// expect /hello?name=geektutu
		c.String(http.StatusOK, "hello %s, you're at %s\n", c.Query("name"), c.Path)
	})
	//middleware
	r.Use(egg.Logger())
	r.Get("/hello/:name", func(c *egg.Context) {
		// expect /hello/geektutu
		c.String(http.StatusOK, "hello %s, you're at %s\n", c.Param("name"), c.Path)
	})

	r.Get("/assets/*filepath", func(c *egg.Context) {
		c.Json(http.StatusOK, egg.H{"filepath": c.Param("filepath")})
	})

	v1 := r.Group("/v1")
	{
		v1.Get("/a", func(c *egg.Context) {
			c.HTML(http.StatusOK, "<h1>V1</h1>")
		})
	}

	r.Run(":8888")

}
