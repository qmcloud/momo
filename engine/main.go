package main

import (
	"bytes"
	"crypto/cipher"
	"crypto/des"
	"database/sql"
	"egg"
	"fmt"
	"math/rand"
	"net/http"
	"reflect"
	"time"

	_ "github.com/go-sql-driver/mysql"
)

const (
	USERNAME = "license"
	PASSWORD = "XbweFaD8HPGM22HZ"
	NETWORK  = "tcp"
	SERVER   = "106.12.99.100"
	PORT     = 3306
	DATABASE = "license"
)

type License struct {
	ID      int64  `db:"id"`
	Prokey  string `db:"prokey"`
	Strinfo int    `db:"strinfo"`
	Type    int    `db:"type"`
	Time    int    `db:"time"`
	Lincese string `db:"lincese"`
}

func main() {
	var r = egg.New()
	r.Use(egg.Logger())
	dsn := fmt.Sprintf("%s:%s@%s(%s:%d)/%s", USERNAME, PASSWORD, NETWORK, SERVER, PORT, DATABASE)
	DB, err := sql.Open("mysql", dsn)

	if err != nil {
		fmt.Printf("Open mysql failed,err:%v\n", err)
	}
	r.Get("/getkey", func(c *egg.Context) {
		if c.Query("key") == "" {
			c.Json(http.StatusOK, egg.H{"key": ""})
			return
		}

		res := StructQueryAllField(DB, c.Query("key"), "strinfo")

		if len(res) > 0 {
			Enc_str := EncryptDES_CBC(c.Query("key"), res[0].Prokey)
			c.Json(http.StatusOK, egg.H{"key": Enc_str})
		} else {
			// 获取当前的key
			key := GetRandomString(8)
			//queryOne(DB)
			Enc_str := EncryptDES_CBC(c.Query("key"), key)
			ret := insertData(DB, key, c.Query("key"), Enc_str)
			if ret {
				c.Json(http.StatusOK, egg.H{"key": Enc_str})
			} else {
				c.Json(http.StatusOK, egg.H{"key": ""})
			}
		}

	})

	r.Get("/ckkey", func(c *egg.Context) {

		res := StructQueryAllField(DB, c.Query("key"), "lincese")

		if len(res) > 0 {
			c.Json(http.StatusOK, egg.H{"key": "ok"})
		} else {
			c.Json(http.StatusOK, egg.H{"key": "error"})
		}
	})
	//middleware
	r.Get("/hello/:name", func(c *egg.Context) {
		// expect /hello/geektutu
		c.String(http.StatusOK, "hello %s, you're at %s\n", c.Param("name"), c.Path)
	})

	r.Get("/assets/*filepath", func(c *egg.Context) {
		c.Json(http.StatusOK, egg.H{"filepath": c.Param("filepath")})
	})

	v1 := r.Group("/v1")
	{
		v1.Get("/getkey", func(c *egg.Context) {
			c.HTML(http.StatusOK, "<h1>V1</h1>")
		})
	}

	defer DB.Close()

	r.Run(":1999")

}

// 查询数据，取所有字段
func StructQueryAllField(DB *sql.DB, str, query string) []License {

	// 通过切片存储
	datas := make([]License, 0)
	rows, _ := DB.Query("select * from auth where "+query+" =? limit 1", str)
	// 遍历
	var list License
	for rows.Next() {
		rows.Scan(&list.ID, &list.Prokey, &list.Strinfo, &list.Type, &list.Time, &list.Lincese)
		datas = append(datas, list)
	}
	return datas

}

func (l License) IsEmpty() bool {
	return reflect.DeepEqual(l, License{})
}

func insertData(DB *sql.DB, key, str, enc_code string) bool {
	result, err := DB.Exec("insert INTO auth (prokey,strinfo,type,time,lincese) values (?,?,?,?,?)", string(key), string(str), 1, time.Now().Unix(), enc_code)
	if err != nil {
		fmt.Printf("Insert failed,err:%v", err)
		return false
	}
	lastInsertID, err := result.LastInsertId() //插入数据的主键id
	if err != nil {
		fmt.Printf("Get lastInsertID failed,err:%v", err)
		return false
	}
	fmt.Println("LastInsertID:", lastInsertID)
	rowsaffected, err := result.RowsAffected() //影响行数
	if err != nil {
		fmt.Printf("Get RowsAffected failed,err:%v", err)
		return false
	}

	fmt.Println("RowsAffected:", rowsaffected)
	return true
}

// 随机生成指定位数的大写字母和数字的组合
func GetRandomString(l int) string {
	str := "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"
	bytes := []byte(str)
	result := []byte{}
	r := rand.New(rand.NewSource(time.Now().UnixNano()))
	for i := 0; i < l; i++ {
		result = append(result, bytes[r.Intn(len(bytes))])
	}
	return string(result)
}

//CBC加密
func EncryptDES_CBC(src, key string) string {
	data := []byte(src)
	keyByte := []byte(key)
	block, err := des.NewCipher(keyByte)
	if err != nil {
		panic("serve is stop please call author, wechat:BCFind5 ")
	}
	data = PKCS5Padding(data, block.BlockSize())
	//获取CBC加密模式
	iv := keyByte //用密钥作为向量(不建议这样使用)
	mode := cipher.NewCBCEncrypter(block, iv)
	out := make([]byte, len(data))
	mode.CryptBlocks(out, data)
	return fmt.Sprintf("%X", out)
}

//明文补码算法
func PKCS5Padding(ciphertext []byte, blockSize int) []byte {
	padding := blockSize - len(ciphertext)%blockSize
	padtext := bytes.Repeat([]byte{byte(padding)}, padding)
	return append(ciphertext, padtext...)
}
