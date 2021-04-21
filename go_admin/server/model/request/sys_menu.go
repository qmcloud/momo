package request

import "leopardlive/model"

// Add menu authority info structure
type AddMenuAuthorityInfo struct {
	Menus       []model.SysBaseMenu
	AuthorityId string
}
