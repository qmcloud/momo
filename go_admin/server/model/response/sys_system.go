package response

import "leopardlive/config"

type SysConfigResponse struct {
	Config config.Server `json:"config"`
}
