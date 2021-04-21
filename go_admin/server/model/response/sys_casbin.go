package response

import "leopardlive/model/request"

type PolicyPathResponse struct {
	Paths []request.CasbinInfo `json:"paths"`
}
