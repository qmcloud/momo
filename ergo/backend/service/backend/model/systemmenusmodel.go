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
	systemMenusFieldNames          = builder.RawFieldNames(&SystemMenus{})
	systemMenusRows                = strings.Join(systemMenusFieldNames, ",")
	systemMenusRowsExpectAutoSet   = strings.Join(stringx.Remove(systemMenusFieldNames, "`id`", "`created_at`", "`updated_at`", "`deleted_at`"), ",")
	systemMenusRowsWithPlaceHolder = strings.Join(stringx.Remove(systemMenusFieldNames, "`id`", "`created_at`", "`updated_at`", "`deleted_at`"), "=?,") + "=?"

	cacheSystemMenusIdPrefix = "cache#systemMenus#id#"
)

type (
	SystemMenusModel interface {
		Insert(data SystemMenus) (sql.Result, error)
		FindOne(id int64) (*SystemMenus, error)
		Update(data SystemMenus) error
		Delete(id int64) error
		List(req utils.ListReq) ([]*SystemMenus, int, error)
		ListParent(req utils.ListReq) ([]*SystemMenus, int, error)
		Tree(ids string) ([]*SystemMenus, error)
		DeleteBatch(ids string) error
		CheckDuplicatePath(path string) (SystemMenus, error)
	}

	defaultSystemMenusModel struct {
		sqlc.CachedConn
		table string
	}

	SystemMenus struct {
		Icon      string    `db:"icon" json:"icon"`             // 附加属性
		CreatedAt time.Time `db:"created_at" json:"created_at"` // 添加日期
		Path      string    `db:"path" json:"path"`             // 路由path
		Hidden    int64     `db:"hidden" json:"hidden"`         // 1是0否在列表隐藏
		ParentId  int64     `db:"parent_id" json:"parent_id"`   // 父菜单ID
		Name      string    `db:"name" json:"name"`             // 路由name
		Component string    `db:"component" json:"component"`   // 对应前端vue文件路径
		Sort      int64     `db:"sort" json:"sort"`             // 排序标记
		Title     string    `db:"title" json:"title"`           // 附加属性
		Id        int64     `db:"id" json:"id"`                 // ID
		UpdatedAt time.Time `db:"updated_at" json:"updated_at"` // 修改日期
		DeletedAt time.Time `db:"deleted_at" json:"deleted_at"` // 删除日期
		//Children  []SystemMenus `json:"children"`
	}
)

func NewSystemMenusModel(conn sqlx.SqlConn, c cache.CacheConf) SystemMenusModel {
	return &defaultSystemMenusModel{
		CachedConn: sqlc.NewConn(conn, c),
		table:      "`system_menus`",
	}
}

func (m *defaultSystemMenusModel) Insert(data SystemMenus) (sql.Result, error) {
	query := fmt.Sprintf("insert into %s (%s) values (?, ?, ?, ?, ?, ?, ?, ?)", m.table, systemMenusRowsExpectAutoSet)
	ret, err := m.ExecNoCache(query, data.Icon, data.Path, data.Hidden, data.ParentId, data.Name, data.Component, data.Sort, data.Title)

	return ret, err
}

func (m *defaultSystemMenusModel) FindOne(id int64) (*SystemMenus, error) {
	systemMenusIdKey := fmt.Sprintf("%s%v", cacheSystemMenusIdPrefix, id)
	var resp SystemMenus
	err := m.QueryRow(&resp, systemMenusIdKey, func(conn sqlx.SqlConn, v interface{}) error {
		query := fmt.Sprintf("select %s from %s where `id` = ? limit 1", systemMenusRows, m.table)
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

func (m *defaultSystemMenusModel) Update(data SystemMenus) error {
	systemMenusIdKey := fmt.Sprintf("%s%v", cacheSystemMenusIdPrefix, data.Id)
	_, err := m.Exec(func(conn sqlx.SqlConn) (result sql.Result, err error) {
		query := fmt.Sprintf("update %s set %s where `id` = ?", m.table, systemMenusRowsWithPlaceHolder)
		fmt.Println(systemMenusRowsWithPlaceHolder)
		fmt.Println(data.Sort)
		fmt.Println(data)
		return conn.Exec(query, data.Icon, data.Path, data.Hidden, data.ParentId, data.Name, data.Component, data.Sort, data.Title, data.Id)
	}, systemMenusIdKey)
	return err
}

func (m *defaultSystemMenusModel) Delete(id int64) error {

	systemMenusIdKey := fmt.Sprintf("%s%v", cacheSystemMenusIdPrefix, id)
	_, err := m.Exec(func(conn sqlx.SqlConn) (result sql.Result, err error) {
		//query := fmt.Sprintf("delete from %s where `id` = ?", m.table)
		//return conn.Exec(query, id)
		query := fmt.Sprintf("update %s set `deleted_at`=? where `id` = ?", m.table)
		return conn.Exec(query, time.Now().Format("2006-01-02 15:04:05"), id)
	}, systemMenusIdKey)
	return err
}

func (m *defaultSystemMenusModel) List(req utils.ListReq) ([]*SystemMenus, int, error) {
	total := 0

	// 条件处理
	whereCondition := "where " + softDeleteFlag
	if req.Keyword != "" {
		whereCondition += "and `name` like '%" + req.Keyword + "%'"
	}

	orderBy := "order by sort asc"
	items := make([]*SystemMenus, 0)
	query := fmt.Sprintf("SELECT %s FROM %s %s %s LIMIT ? OFFSET ?", systemMenusRows, m.table, whereCondition, orderBy)
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

/**
获取菜单树
ids 对应角色的菜单权，空：所有菜单
*/
func (m *defaultSystemMenusModel) Tree(ids string) ([]*SystemMenus, error) {
	// 条件处理
	whereCondition := "where `hidden` = 0 "
	if ids != "" {
		whereCondition += fmt.Sprintf(" and `id` in (%s)", ids)
	}

	orderBy := "order by `sort` asc"
	items := make([]*SystemMenus, 0)
	query := fmt.Sprintf("SELECT %s FROM %s %s %s ", systemMenusRows, m.table, whereCondition, orderBy)

	//获取记录
	err := m.CachedConn.QueryRowsNoCache(&items, query)
	if err != nil {
		logx.Errorf("usersSex.findOne error, sex=%d, err=%s", -1, err.Error())
		if err == sqlx.ErrNotFound {
			return nil, ErrNotFound
		}
		return nil, err
	}

	return items, nil
}

func (m *defaultSystemMenusModel) ListParent(req utils.ListReq) ([]*SystemMenus, int, error) {
	total := 0

	// 条件处理
	whereCondition := "where " + softDeleteFlag
	whereCondition += " and parent_id = 0 "

	orderBy := "order by sort asc"
	items := make([]*SystemMenus, 0)
	query := fmt.Sprintf("SELECT %s FROM %s %s %s LIMIT ? OFFSET ?", systemMenusRows, m.table, whereCondition, orderBy)
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

func (m *defaultSystemMenusModel) DeleteBatch(ids string) error {
	query := fmt.Sprintf("update %s set `deleted_at`=? where `id` in (%s)", m.table, ids)
	_, err := m.ExecNoCache(query, time.Now().Format("2006-01-02 15:04:05"))

	// 删除缓存
	idArr := strings.Split(ids, ",")
	for _, v := range idArr {
		systemUserIdKey := fmt.Sprintf("%s%v", cacheSystemMenusIdPrefix, v)
		m.DelCache(systemUserIdKey)
	}
	return err
}
func (m *defaultSystemMenusModel) CheckDuplicatePath(path string) (SystemMenus, error) {
	var resp SystemMenus
	queryString := fmt.Sprintf("select %s from %s where %s and `path` = ? limit 1", systemMenusRows, m.table, softDeleteFlag)
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
func (m *defaultSystemMenusModel) formatPrimary(primary interface{}) string {
	return fmt.Sprintf("%s%v", cacheSystemMenusIdPrefix, primary)
}

func (m *defaultSystemMenusModel) queryPrimary(conn sqlx.SqlConn, v, primary interface{}) error {
	query := fmt.Sprintf("select %s from %s where `id` = ? limit 1", systemMenusRows, m.table)
	return conn.QueryRow(v, query, primary)
}
