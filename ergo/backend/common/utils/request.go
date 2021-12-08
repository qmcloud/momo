package utils

type ListReq struct {
	Page     int64  `json:"page,optional,default=1" form:"page,optional,default=1"`
	PageSize int64  `json:"pageSize,optional,default=10" form:"pageSize,optional,default=10"`
	Keyword  string `json:"keyword,optional" form:"pageSize,optional"`
}
