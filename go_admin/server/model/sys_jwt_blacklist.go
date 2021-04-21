package model

import (
	"leopardlive/global"
)

type JwtBlacklist struct {
	global.GVA_MODEL
	Jwt string `gorm:"type:text;comment:jwt"`
}
