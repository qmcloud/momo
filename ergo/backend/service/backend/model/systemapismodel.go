package model

import (
	"backend/common/utils"
	"database/sql"
	"fmt"
	"github.com/zeromicro/go-zero/core/logx"
	"strings"
	"time"

	"github.com/zeromicro/go-zero/core/stores/builder"
	"github.com/zeromicro/go-zero/core/stores/cache"
	"github.com/zeromicro/go-zero/core/stores/sqlc"
	"github.com/zeromicro/go-zero/core/stores/sqlx"
	"github.com/zeromicro/go-zero/core/stringx"
)

var (
	systemApisFieldNames          = builder.RawFieldNames(&SystemApis{})
	systemApisRows                = strings.Join(systemApisFieldNames, ",")
	systemApisRowsExpectAutoSet   = strings.Join(stringx.Remove(systemApisFieldNames, "`id`", "`created_at`", "`updated_at`", "`deleted_at`"), ",")
	systemApisRowsWithPlaceHolder = strings.Join(stringx.Remove(systemApisFieldNames, "`id`", "`created_at`", "`updated_at`", "`deleted_at`"), "=?,") + "=?"

	cacheSystemApisIdPrefix = "cache#systemApis#id#"
)

type (
	SystemApisModel interface {
		Insert(data SystemApis) (sql.Result, error)
		FindOne(id int64) (*SystemApis, error)
		Update(data SystemApis) error
		Delete(id int64) error
		List(req utils.ListReq) ([]*SystemApis, int, error)
		DeleteBatch(ids string) error
		CheckDuplicatePath(path string) (SystemApis, error)
	}

	defaultSystemApisModel struct {
		sqlc.CachedConn
		table string
	}

	SystemApis struct {
		Path        string    `db:"path" json:"path"`               // api路径
		Description string    `db:"description" json:"description"` // api中文描述
		ApiGroup    string    `db:"api_group" json:"api_group"`     // api组
		Method      string    `db:"method" json:"method"`           // 请求方法
		Id          int64     `db:"id" json:"id"`
		CreatedAt   time.Time `db:"created_at" json:"created_at"`
		UpdatedAt   time.Time `db:"updated_at" json:"updated_at"`
		DeletedAt   time.Time `db:"deleted_at" json:"deleted_at"`
	}
)

func NewSystemApisModel(conn sqlx.SqlConn, c cache.CacheConf) SystemApisModel {
	return &defaultSystemApisModel{
		CachedConn: sqlc.NewConn(conn, c),
		table:      "`system_apis`",
	}
}

func (m *defaultSystemApisModel) Insert(data SystemApis) (sql.Result, error) {
	query := fmt.Sprintf("insert into %s (%s) values (?, ?, ?, ?)", m.table, systemApisRowsExpectAutoSet)
	ret, err := m.ExecNoCache(query, data.Path, data.Description, data.ApiGroup, data.Method)

	return ret, err
}

func (m *defaultSystemApisModel) FindOne(id int64) (*SystemApis, error) {
	systemApisIdKey := fmt.Sprintf("%s%v", cacheSystemApisIdPrefix, id)
	var resp SystemApis
	err := m.QueryRow(&resp, systemApisIdKey, func(conn sqlx.SqlConn, v interface{}) error {
		query := fmt.Sprintf("select %s from %s where `id` = ? limit 1", systemApisRows, m.table)
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

func (m *defaultSystemApisModel) Update(data SystemApis) error {
	systemApisIdKey := fmt.Sprintf("%s%v", cacheSystemApisIdPrefix, data.Id)
	_, err := m.Exec(func(conn sqlx.SqlConn) (result sql.Result, err error) {
		query := fmt.Sprintf("update %s set %s where `id` = ?", m.table, systemApisRowsWithPlaceHolder)
		return conn.Exec(query, data.Path, data.Description, data.ApiGroup, data.Method, data.Id)
	}, systemApisIdKey)
	return err
}

func (m *defaultSystemApisModel) Delete(id int64) error {

	systemApisIdKey := fmt.Sprintf("%s%v", cacheSystemApisIdPrefix, id)
	_, err := m.Exec(func(conn sqlx.SqlConn) (result sql.Result, err error) {
		//query := fmt.Sprintf("delete from %s where `id` = ?", m.table)
		//return conn.Exec(query, id)
		query := fmt.Sprintf("update %s set `deleted_at`=? where `id` = ?", m.table)
		return conn.Exec(query, time.Now().Format("2006-01-02 15:04:05"), id)
	}, systemApisIdKey)
	return err
}

func (m *defaultSystemApisModel) List(req utils.ListReq) ([]*SystemApis, int, error) {
	total := 0

	// 条件处理
	whereCondition := "where " + softDeleteFlag
	if req.Keyword != "" {
		whereCondition += "and (`api_group` like '%" + req.Keyword + "%' or `path` like '%" + req.Keyword + "%' or `description` like '%" + req.Keyword + "%')"
	}

	orderBy := "order by api_group asc,id desc"
	items := make([]*SystemApis, 0)
	query := fmt.Sprintf("SELECT %s FROM %s %s %s LIMIT ? OFFSET ?", systemApisRows, m.table, whereCondition, orderBy)
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
func (m *defaultSystemApisModel) DeleteBatch(ids string) error {
	query := fmt.Sprintf("update %s set `deleted_at`=? where `id` in (%s)", m.table, ids)
	_, err := m.ExecNoCache(query, time.Now().Format("2006-01-02 15:04:05"))

	// 删除缓存
	idArr := strings.Split(ids, ",")
	for _, v := range idArr {
		systemUserIdKey := fmt.Sprintf("%s%v", cacheSystemApisIdPrefix, v)
		m.DelCache(systemUserIdKey)
	}
	return err
}
func (m *defaultSystemApisModel) CheckDuplicatePath(path string) (SystemApis, error) {
	var resp SystemApis
	queryString := fmt.Sprintf("select %s from %s where %s and `path` = ? limit 1", systemApisRows, m.table, softDeleteFlag)
	err := m.CachedConn.QueryRowNoCache(&resp, queryString, path)
	switch err {
	case nil:
		return resp, nil
	case sqlc.ErrNotFound:
		return resp, ErrNotFound
	default:
		return resp, err
	}
}

func (m *defaultSystemApisModel) formatPrimary(primary interface{}) string {
	return fmt.Sprintf("%s%v", cacheSystemApisIdPrefix, primary)
}

func (m *defaultSystemApisModel) queryPrimary(conn sqlx.SqlConn, v, primary interface{}) error {
	query := fmt.Sprintf("select %s from %s where `id` = ? limit 1", systemApisRows, m.table)
	return conn.QueryRow(v, query, primary)
}
