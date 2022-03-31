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
	systemRoleMenusRows                = "`id`,`menu_id`,`role_id`"
	systemRoleMenusRowsExpectAutoSet   = "`menu_id`,`role_id`"
	systemRoleMenusRowsWithPlaceHolder = "`menu_id`=?,`role_id`=?"

	cacheSystemRoleMenusIdPrefix = "cache#systemRoleMenus#id#"
)

type (
	SystemRoleMenusModel interface {
		Insert(menuIds string, roleId int64) (sql.Result, error)
		FindOne(id int64) (*SystemRoleMenus, error)
		Update(data SystemRoleMenus) error
		Delete(id int64) error
		List(req utils.ListReq) ([]*SystemRoleMenus, int, error)
		ListByRoleId(roleId int64) ([]*SystemRoleMenus, error)
		DeleteBatch(ids string) error
		CheckDuplicate(fieldname string) (SystemRoleMenus, error)
	}

	defaultSystemRoleMenusModel struct {
		sqlc.CachedConn
		table string
	}

	SystemRoleMenus struct {
		Id     int64 `db:"id" json:"id"`           //
		MenuId int64 `db:"menu_id" json:"menu_id"` // 菜单ID
		RoleId int64 `db:"role_id" json:"role_id"` // 角色ID
	}
)

// 角色菜单关系
func NewSystemRoleMenusModel(conn sqlx.SqlConn, c cache.CacheConf) SystemRoleMenusModel {
	return &defaultSystemRoleMenusModel{
		CachedConn: sqlc.NewConn(conn, c),
		table:      "`system_role_menus`",
	}
}

func (m *defaultSystemRoleMenusModel) Insert(menuIds string, roleId int64) (sql.Result, error) {
	// 删除原role_id数据
	delQuery := fmt.Sprintf("delete from %s where `role_id` = ?", m.table)
	m.ExecNoCache(delQuery, roleId)
	// 批量添加新值
	idArr := strings.Split(menuIds, ",")
	for _, v := range idArr {
		query := fmt.Sprintf("insert into %s (%s) values (?,?)", m.table, systemRoleMenusRowsExpectAutoSet)
		m.ExecNoCache(query, v, roleId)
	}
	return nil, nil
}

func (m *defaultSystemRoleMenusModel) FindOne(id int64) (*SystemRoleMenus, error) {
	systemRoleMenusIdKey := fmt.Sprintf("%s%v", cacheSystemRoleMenusIdPrefix, id)
	var resp SystemRoleMenus
	err := m.QueryRow(&resp, systemRoleMenusIdKey, func(conn sqlx.SqlConn, v interface{}) error {
		query := fmt.Sprintf("select %s from %s where `id` = ? limit 1", systemRoleMenusRows, m.table)
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

func (m *defaultSystemRoleMenusModel) Update(data SystemRoleMenus) error {
	systemRoleMenusIdKey := fmt.Sprintf("%s%v", cacheSystemRoleMenusIdPrefix, data.Id)
	_, err := m.Exec(func(conn sqlx.SqlConn) (result sql.Result, err error) {
		query := fmt.Sprintf("update %s set %s where `id` = ?", m.table, systemRoleMenusRowsWithPlaceHolder)
		return conn.Exec(query, data.MenuId, data.RoleId, data.Id)
	}, systemRoleMenusIdKey)
	return err
}

func (m *defaultSystemRoleMenusModel) Delete(id int64) error {
	systemRoleMenusIdKey := fmt.Sprintf("%s%v", cacheSystemRoleMenusIdPrefix, id)
	_, err := m.Exec(func(conn sqlx.SqlConn) (result sql.Result, err error) {
		query := fmt.Sprintf("delete from  %s where `id` = ?", m.table)
		return conn.Exec(query, id)
	}, systemRoleMenusIdKey)
	return err
}

func (m *defaultSystemRoleMenusModel) List(req utils.ListReq) ([]*SystemRoleMenus, int, error) {
	total := 0

	// 条件处理
	whereCondition := ""

	orderBy := "order by id asc"
	items := make([]*SystemRoleMenus, 0)
	query := fmt.Sprintf("SELECT %s FROM %s %s %s LIMIT ? OFFSET ?", systemRoleMenusRows, m.table, whereCondition, orderBy)
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

func (m *defaultSystemRoleMenusModel) ListByRoleId(roleId int64) ([]*SystemRoleMenus, error) {

	// 条件处理
	whereCondition := " where `role_id` = ?"

	orderBy := "order by id asc"
	items := make([]*SystemRoleMenus, 0)
	query := fmt.Sprintf("SELECT %s FROM %s %s %s", systemRoleMenusRows, m.table, whereCondition, orderBy)

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

func (m *defaultSystemRoleMenusModel) DeleteBatch(ids string) error {
	query := fmt.Sprintf("delete from  %s where `id` in (%s)", m.table, ids)
	_, err := m.ExecNoCache(query)

	// 删除缓存
	idArr := strings.Split(ids, ",")
	for _, v := range idArr {
		systemUserIdKey := fmt.Sprintf("%s%v", cacheSystemRoleMenusIdPrefix, v)
		m.DelCache(systemUserIdKey)
	}
	return err
}
func (m *defaultSystemRoleMenusModel) CheckDuplicate(fieldname string) (SystemRoleMenus, error) {
	var resp SystemRoleMenus
	queryString := fmt.Sprintf("select %s from %s where %s and `name` = ? limit 1", systemRoleMenusRows, m.table, softDeleteFlag)
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
