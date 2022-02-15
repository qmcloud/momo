package model

import (
	"backend/common/utils"
	"database/sql"
	"fmt"
	"github.com/zeromicro/go-zero/core/logx"
	"strings"
	"time"

	"github.com/zeromicro/go-zero/core/stores/cache"
	"github.com/zeromicro/go-zero/core/stores/sqlc"
	"github.com/zeromicro/go-zero/core/stores/sqlx"
)

var (
	systemRolesRows                = "`created_at`,`deleted_at`,`id`,`name`,`parent_id`,`sort`,`updated_at`"
	systemRolesRowsExpectAutoSet   = "`name`,`parent_id`,`sort`"
	systemRolesRowsWithPlaceHolder = "`name`=?,`parent_id`=?,`sort`=?"
	cacheSystemRolesIdPrefix       = "cache#systemRoles#id#"
)

type (
	SystemRolesModel interface {
		Insert(data SystemRoles) (sql.Result, error)
		FindOne(id int64) (*SystemRoles, error)
		Update(data SystemRoles) error
		Delete(id int64) error
		List(req utils.ListReq) ([]*SystemRoles, int, error)
		ListParent(req utils.ListReq) ([]*SystemRoles, int, error)
		DeleteBatch(ids string) error
		CheckDuplicatePath(path string) (SystemRoles, error)
	}

	defaultSystemRolesModel struct {
		sqlc.CachedConn
		table string
	}

	SystemRoles struct {
		CreatedAt string `db:"created_at" json:"created_at"` //
		DeletedAt string `db:"deleted_at" json:"deleted_at"` //
		Id        int64  `db:"id" json:"id"`                 //
		Name      string `db:"name" json:"name"`             // 角色名
		ParentId  int64  `db:"parent_id" json:"parent_id"`   // 父级ID
		Sort      int64  `db:"sort" json:"sort"`             // 排序
		UpdatedAt string `db:"updated_at" json:"updated_at"` //
	}
)

func NewSystemRolesModel(conn sqlx.SqlConn, c cache.CacheConf) SystemRolesModel {
	return &defaultSystemRolesModel{
		CachedConn: sqlc.NewConn(conn, c),
		table:      "`system_roles`",
	}
}

func (m *defaultSystemRolesModel) Insert(data SystemRoles) (sql.Result, error) {
	query := fmt.Sprintf("insert into %s (%s) values (?, ?, ?)", m.table, systemRolesRowsExpectAutoSet)
	ret, err := m.ExecNoCache(query, data.Name, data.ParentId, data.Sort)

	return ret, err
}

func (m *defaultSystemRolesModel) FindOne(id int64) (*SystemRoles, error) {
	systemRolesIdKey := fmt.Sprintf("%s%v", cacheSystemRolesIdPrefix, id)
	var resp SystemRoles
	err := m.QueryRow(&resp, systemRolesIdKey, func(conn sqlx.SqlConn, v interface{}) error {
		query := fmt.Sprintf("select %s from %s where `id` = ? limit 1", systemRolesRows, m.table)
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

func (m *defaultSystemRolesModel) Update(data SystemRoles) error {
	systemRolesIdKey := fmt.Sprintf("%s%v", cacheSystemRolesIdPrefix, data.Id)
	_, err := m.Exec(func(conn sqlx.SqlConn) (result sql.Result, err error) {
		query := fmt.Sprintf("update %s set %s where `id` = ?", m.table, systemRolesRowsWithPlaceHolder)
		return conn.Exec(query, data.Name, data.ParentId, data.Sort, data.Id)
	}, systemRolesIdKey)
	return err
}

func (m *defaultSystemRolesModel) Delete(id int64) error {

	systemRolesIdKey := fmt.Sprintf("%s%v", cacheSystemRolesIdPrefix, id)
	_, err := m.Exec(func(conn sqlx.SqlConn) (result sql.Result, err error) {
		//query := fmt.Sprintf("delete from %s where `id` = ?", m.table)
		//return conn.Exec(query, id)
		query := fmt.Sprintf("update %s set `deleted_at`=? where `id` = ?", m.table)
		return conn.Exec(query, time.Now().Format("2006-01-02 15:04:05"), id)
	}, systemRolesIdKey)
	return err
}

func (m *defaultSystemRolesModel) List(req utils.ListReq) ([]*SystemRoles, int, error) {
	total := 0

	// 条件处理
	whereCondition := "where " + softDeleteFlag
	if req.Keyword != "" {
		whereCondition += "and `name` like '%" + req.Keyword + "%'"
	}

	orderBy := "order by sort asc"
	items := make([]*SystemRoles, 0)
	query := fmt.Sprintf("SELECT %s FROM %s %s %s LIMIT ? OFFSET ?", systemRolesRows, m.table, whereCondition, orderBy)
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

func (m *defaultSystemRolesModel) ListParent(req utils.ListReq) ([]*SystemRoles, int, error) {
	total := 0

	// 条件处理
	whereCondition := "where " + softDeleteFlag
	whereCondition += " and `parent_id` = 0 "

	orderBy := "order by sort asc"
	items := make([]*SystemRoles, 0)
	query := fmt.Sprintf("SELECT %s FROM %s %s %s LIMIT ? OFFSET ?", systemRolesRows, m.table, whereCondition, orderBy)
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

func (m *defaultSystemRolesModel) DeleteBatch(ids string) error {
	query := fmt.Sprintf("update %s set `deleted_at`=? where `id` in (%s)", m.table, ids)
	_, err := m.ExecNoCache(query, time.Now().Format("2006-01-02 15:04:05"))

	// 删除缓存
	idArr := strings.Split(ids, ",")
	for _, v := range idArr {
		systemUserIdKey := fmt.Sprintf("%s%v", cacheSystemRolesIdPrefix, v)
		m.DelCache(systemUserIdKey)
	}
	return err
}
func (m *defaultSystemRolesModel) CheckDuplicatePath(path string) (SystemRoles, error) {
	var resp SystemRoles
	queryString := fmt.Sprintf("select %s from %s where %s and `name` = ? limit 1", systemRolesRows, m.table, softDeleteFlag)
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

func (m *defaultSystemRolesModel) formatPrimary(primary interface{}) string {
	return fmt.Sprintf("%s%v", cacheSystemRolesIdPrefix, primary)
}

func (m *defaultSystemRolesModel) queryPrimary(conn sqlx.SqlConn, v, primary interface{}) error {
	query := fmt.Sprintf("select %s from %s where `id` = ? limit 1", systemRolesRows, m.table)
	return conn.QueryRow(v, query, primary)
}
