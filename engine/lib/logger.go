package egg

import (
	"log"
	"time"
)

func Logger() HandlerFunc {
	return func(c *Context) {
		// Start timer
		t := time.Now()
		// Process request
		c.Next()
		// Calculate resolution time
		log.Printf("[%d] %s in %v", c.StatusCode, c.Req.RequestURI, time.Since(t))
	}
}

func Test() HandlerFunc {
	return func(c *Context) {
		log.Printf("[%d] %s", c.StatusCode, c.Req.RequestURI)
	}
}
