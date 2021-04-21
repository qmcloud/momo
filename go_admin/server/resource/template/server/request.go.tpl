package request

import "leopardlive/model"

type {{.StructName}}Search struct{
    model.{{.StructName}}
    PageInfo
}