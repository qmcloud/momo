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

	r.Get("/hello/:name", func(c *egg.Context) {
		// expect /hello/geektutu
		c.String(http.StatusOK, "hello %s, you're at %s\n", c.Param("name"), c.Path)
	})

	r.Get("/assets/*filepath", func(c *egg.Context) {
		c.Json(http.StatusOK, egg.H{"filepath": c.Param("filepath")})
	})

	r.Run(":8888")

}
