package xgorm

import (
	"gorm.io/driver/mysql"
	"gorm.io/gorm"
)

func NewGorm(dsn string) *gorm.DB {
	db, err := gorm.Open(mysql.Open(dsn), &gorm.Config{})
	if err != nil {
		panic(err)
	}
	return db
}
