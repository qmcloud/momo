package model

import (
	"backend/common/utils"
	"database/sql"
	"fmt"
	"github.com/zeromicro/go-zero/core/logx"
	"github.com/zeromicro/go-zero/core/stores/cache"
	"github.com/zeromicro/go-zero/core/stores/sqlc"
	"github.com/zeromicro/go-zero/core/stores/sqlx"
	"strings"
)

var (
	//bulkInserter
	//systemRoleApisRowsExpectAutoSet   = "`id`,`p_type`,`v0`,`v1`,`v2`,`v3`,`v4`,`v5`"
	systemRoleApisRows                = "`p_type`,`v0`,`v1`,`v2`,`v3`,`v4`,`v5`"
	systemRoleApisRowsExpectAutoSet   = "`p_type`,`v0`,`v1`,`v2`,`v3`,`v4`,`v5`"
	systemRoleApisRowsWithPlaceHolder = "`p_type`=?,`v0`=?,`v1`=?,`v2`=?,`v3`=?,`v4`=?,`v5`=?"

	cacheSystemRoleApisIdPrefix = "cache#systemRoleApis#id#"
)

type (
	SystemRoleApisModel interface {
		Insert(data []SystemRoleApis, roleId int64) (sql.Result, error)
		FindOne(id int64) (*SystemRoleApis, error)
		//Update(data SystemRoleApis) error
		Delete(id int64) error
		List(req utils.ListReq) ([]*SystemRoleApis, int, error)
		ListByRoleId(roleId int64) ([]*SystemRoleApis, error)
		DeleteBatch(ids string) error
		CheckDuplicate(fieldname string) (SystemRoleApis, error)
	}

	defaultSystemRoleApisModel struct {
		sqlc.CachedConn
		table string
		Bulk  sqlx.BulkInserter
	}

	SystemRoleApis struct {
		PType string `db:"p_type" json:"p_type"` //
		V0    string `db:"v0" json:"v0"`         //
		V1    string `db:"v1" json:"v1"`         //
		V2    string `db:"v2" json:"v2"`         //
		V3    string `db:"v3" json:"v3"`         //
		V4    string `db:"v4" json:"v4"`         //
		V5    string `db:"v5" json:"v5"`         //

	}
)

// 角色Api关系
func NewSystemRoleApisModel(conn sqlx.SqlConn, c cache.CacheConf) SystemRoleApisModel {
	//defer func() {
	//	recover()
	//}()
	//insertsql := fmt.Sprintf("insert into %s (%s) values (?,?,?,?,?,?,?,?)", "`system_role_apis`", systemRoleApisRowsExpectAutoSet)
	//bulkInserter, err := sqlx.NewBulkInserter(conn, insertsql)
	//if err != nil {
	//	logx.Error("Init bulkInsert Faild")
	//	panic("Init bulkInsert Faild")
	//	return nil
	//}
	return &defaultSystemRoleApisModel{
		CachedConn: sqlc.NewConn(conn, c),
		table:      "`system_role_apis`",
		//Bulk:       *bulkInserter,
	}
}

func (m *defaultSystemRoleApisModel) Insert(data []SystemRoleApis, roleId int64) (sql.Result, error) {
	// 删除原role_id数据
	delQuery := fmt.Sprintf("delete from %s where `v0` = ?", m.table)
	m.ExecNoCache(delQuery, roleId)
	// 批量添加新值

	query := fmt.Sprintf("insert into %s (%s) values (?,?,?,?,?,?,?)", m.table, systemRoleApisRowsExpectAutoSet)
	for _, v := range data {
		m.ExecNoCache(query, v.PType, v.V0, v.V1, v.V2, v.V3, v.V4, v.V5)
	}
	// todo: 批量处理优化
	//v := data[0]
	//err := m.Bulk.Insert(v.Id, v.PType, v.V0, v.V1, v.V2, v.V3, v.V4, v.V5)
	//if err != nil {
	//	fmt.Println("---------------error---------------")
	//	fmt.Printf("%v\n", err)
	//	fmt.Println("---------------error---------------")
	//}
	return nil, nil
}

func (m *defaultSystemRoleApisModel) FindOne(id int64) (*SystemRoleApis, error) {
	systemRoleApisIdKey := fmt.Sprintf("%s%v", cacheSystemRoleApisIdPrefix, id)
	var resp SystemRoleApis
	err := m.QueryRow(&resp, systemRoleApisIdKey, func(conn sqlx.SqlConn, v interface{}) error {
		query := fmt.Sprintf("select %s from %s where `id` = ? limit 1", systemRoleApisRows, m.table)
		return conn.QueryRow(v, query, id)
	})
	switch err {
	case nil:
		return &resp, nil
	case sqlc.ErrNotFound:
		return nil, ErrNotFound
	default:
		return nil, err
	}
}

//func (m *defaultSystemRoleApisModel) Update(data SystemRoleApis) error {
//	systemRoleApisIdKey := fmt.Sprintf("%s%v", cacheSystemRoleApisIdPrefix, data.Id)
//	_, err := m.Exec(func(conn sqlx.SqlConn) (result sql.Result, err error) {
//		query := fmt.Sprintf("update %s set %s where `id` = ?", m.table, systemRoleApisRowsWithPlaceHolder)
//		return conn.Exec(query, data.PType, data.V0, data.V1, data.V2, data.V3, data.V4, data.V5, data.Id)
//	}, systemRoleApisIdKey)
//	return err
//}

func (m *defaultSystemRoleApisModel) Delete(id int64) error {
	systemRoleApisIdKey := fmt.Sprintf("%s%v", cacheSystemRoleApisIdPrefix, id)
	_, err := m.Exec(func(conn sqlx.SqlConn) (result sql.Result, err error) {
		query := fmt.Sprintf("delete from  %s where `id` = ?", m.table)
		return conn.Exec(query, id)
	}, systemRoleApisIdKey)
	return err
}

func (m *defaultSystemRoleApisModel) List(req utils.ListReq) ([]*SystemRoleApis, int, error) {
	total := 0

	// 条件处理
	whereCondition := ""
	if req.Keyword != "" {
		whereCondition += "where `name` like '%" + req.Keyword + "%'"
	}

	orderBy := ""
	items := make([]*SystemRoleApis, 0)
	query := fmt.Sprintf("SELECT %s FROM %s %s %s LIMIT ? OFFSET ?", systemRoleApisRows, m.table, whereCondition, orderBy)
	queryCount := fmt.Sprintf("SELECT count(1) FROM %s %s", m.table, whereCondition)
	err := m.CachedConn.QueryRowNoCache(&total, queryCount)

	// 查询错误
	if err != nil {
		return items, total, err
	}

	// 没有记录
	if total == 0 {
		return items, total, nil
	}

	//获取记录
	err = m.CachedConn.QueryRowsNoCache(&items, query, req.PageSize, req.PageSize*(req.Page-1))
	if err != nil {
		logx.Errorf("usersSex.findOne error, sex=%d, err=%s", req.Page, err.Error())
		if err == sqlx.ErrNotFound {
			return nil, total, ErrNotFound
		}
		return nil, total, err
	}

	return items, total, nil
}

func (m *defaultSystemRoleApisModel) ListByRoleId(roleId int64) ([]*SystemRoleApis, error) {

	// 条件处理
	whereCondition := " where `v0` = ?"

	orderBy := ""
	items := make([]*SystemRoleApis, 0)
	query := fmt.Sprintf("SELECT %s FROM %s %s %s", systemRoleApisRows, m.table, whereCondition, orderBy)

	//获取记录
	err := m.CachedConn.QueryRowsNoCache(&items, query, roleId)
	if err != nil {
		logx.Errorf("usersSex.findOne error, sex=%d, err=%s", roleId, err.Error())
		if err == sqlx.ErrNotFound {
			return nil, ErrNotFound
		}
		return nil, err
	}
	return items, nil
}

func (m *defaultSystemRoleApisModel) DeleteBatch(ids string) error {
	query := fmt.Sprintf("delete from %s  where `id` in (%s)", m.table, ids)
	_, err := m.ExecNoCache(query)

	// 删除缓存
	idArr := strings.Split(ids, ",")
	for _, v := range idArr {
		systemUserIdKey := fmt.Sprintf("%s%v", cacheSystemRoleApisIdPrefix, v)
		m.DelCache(systemUserIdKey)
	}
	return err
}
func (m *defaultSystemRoleApisModel) CheckDuplicate(fieldname string) (SystemRoleApis, error) {
	var resp SystemRoleApis
	queryString := fmt.Sprintf("select %s from %s where `name` = ? limit 1", systemRoleApisRows, m.table)
	err := m.CachedConn.QueryRowNoCache(&resp, queryString, fieldname)
	switch err {
	case nil:
		return resp, nil
	case sqlc.ErrNotFound:
		return resp, ErrNotFound
	default:
		return resp, err
	}
}
