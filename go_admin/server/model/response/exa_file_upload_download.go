package response

import "leopardlive/model"

type ExaFileResponse struct {
	File model.ExaFileUploadAndDownload `json:"file"`
}
