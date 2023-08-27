package main

import (
	"fmt"
	"net/http"
)

func setupCORS(w *http.ResponseWriter) {
	(*w).Header().Set("Access-Control-Allow-Origin", "*")
	(*w).Header().Set("Access-Control-Allow-Methods", "POST, GET, OPTIONS, PUT, DELETE")
	(*w).Header().Set("Access-Control-Allow-Headers", "Accept, Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token, Authorization")
}

func HandlerHttp(w http.ResponseWriter, r *http.Request) {
	setupCORS(&w)
	if r.Method == "OPTIONS" {
		return
	}
	w.Write([]byte("hello, world"))
}

func main() {

	http.HandleFunc("/", HandlerHttp)
	fmt.Println("website is runing at localhost:8888")
	http.ListenAndServe(":8888", http.FileServer(http.Dir("./")))
}
