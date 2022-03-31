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
	"time"
)

var (
	systemDepartmentsRows                = "`ancestors`,`create_by`,`created_at`,`deleted_at`,`email`,`id`,`leader`,`name`,`parent_id`,`phone`,`sort`,`status`,`update_by`,`updated_at`"
	systemDepartmentsRowsExpectAutoSet   = "`ancestors`,`create_by`,`email`,`leader`,`name`,`parent_id`,`phone`,`sort`,`status`,`update_by`"
	systemDepartmentsRowsWithPlaceHolder = "`ancestors`=?,`create_by`=?,`email`=?,`leader`=?,`name`=?,`parent_id`=?,`phone`=?,`sort`=?,`status`=?,`update_by`=?"

	cacheSystemDepartmentsIdPrefix = "cache#systemDepartments#id#"
)

type (
	SystemDepartmentsModel interface {
		Insert(data SystemDepartments) (sql.Result, error)
		FindOne(id int64) (*SystemDepartments, error)
		Update(data SystemDepartments) error
		Delete(id int64) error
		List(req utils.ListReq) ([]*SystemDepartments, int, error)
		ListParent(req utils.ListReq) ([]*SystemDepartments, int, error)
		DeleteBatch(ids string) error
		CheckDuplicate(fieldname string) (SystemDepartments, error)
	}

	defaultSystemDepartmentsModel struct {
		sqlc.CachedConn
		table string
	}

	SystemDepartments struct {
		Ancestors string `db:"ancestors" json:"ancestors"`   // 祖级列表
		CreateBy  string `db:"create_by" json:"create_by"`   // 创建者
		CreatedAt string `db:"created_at" json:"created_at"` //
		DeletedAt string `db:"deleted_at" json:"deleted_at"` //
		Email     string `db:"email" json:"email"`           // 邮箱
		Id        int64  `db:"id" json:"id"`                 //
		Leader    string `db:"leader" json:"leader"`         // 负责人
		Name      string `db:"name" json:"name"`             // 部门名称
		ParentId  int64  `db:"parent_id" json:"parent_id"`   // 父级ID
		Phone     string `db:"phone" json:"phone"`           // 联系电话
		Sort      int64  `db:"sort" json:"sort"`             // 排序
		Status    int64  `db:"status" json:"status"`         // 部门状态（0正常 1停用）
		UpdateBy  string `db:"update_by" json:"update_by"`   // 更新者
		UpdatedAt string `db:"updated_at" json:"updated_at"` //

	}
)

// 部门管理
func NewSystemDepartmentsModel(conn sqlx.SqlConn, c cache.CacheConf) SystemDepartmentsModel {
	return &defaultSystemDepartmentsModel{
		CachedConn: sqlc.NewConn(conn, c),
		table:      "`system_departments`",
	}
}

func (m *defaultSystemDepartmentsModel) Insert(data SystemDepartments) (sql.Result, error) {
	query := fmt.Sprintf("insert into %s (%s) values (?,?,?,?,?,?,?,?,?,?)", m.table, systemDepartmentsRowsExpectAutoSet)
	ret, err := m.ExecNoCache(query, data.Ancestors, data.CreateBy, data.Email, data.Leader, data.Name, data.ParentId, data.Phone, data.Sort, data.Status, data.UpdateBy)

	return ret, err
}

func (m *defaultSystemDepartmentsModel) FindOne(id int64) (*SystemDepartments, error) {
	systemDepartmentsIdKey := fmt.Sprintf("%s%v", cacheSystemDepartmentsIdPrefix, id)
	var resp SystemDepartments
	err := m.QueryRow(&resp, systemDepartmentsIdKey, func(conn sqlx.SqlConn, v interface{}) error {
		query := fmt.Sprintf("select %s from %s where `id` = ? limit 1", systemDepartmentsRows, m.table)
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

func (m *defaultSystemDepartmentsModel) Update(data SystemDepartments) error {
	systemDepartmentsIdKey := fmt.Sprintf("%s%v", cacheSystemDepartmentsIdPrefix, data.Id)
	_, err := m.Exec(func(conn sqlx.SqlConn) (result sql.Result, err error) {
		query := fmt.Sprintf("update %s set %s where `id` = ?", m.table, systemDepartmentsRowsWithPlaceHolder)
		return conn.Exec(query, data.Ancestors, data.CreateBy, data.Email, data.Leader, data.Name, data.ParentId, data.Phone, data.Sort, data.Status, data.UpdateBy, data.Id)
	}, systemDepartmentsIdKey)
	return err
}

func (m *defaultSystemDepartmentsModel) Delete(id int64) error {
	systemDepartmentsIdKey := fmt.Sprintf("%s%v", cacheSystemDepartmentsIdPrefix, id)
	_, err := m.Exec(func(conn sqlx.SqlConn) (result sql.Result, err error) {
		query := fmt.Sprintf("update %s set `deleted_at`=? where `id` = ?", m.table)
		return conn.Exec(query, time.Now().Format("2006-01-02 15:04:05"), id)
	}, systemDepartmentsIdKey)
	return err
}

func (m *defaultSystemDepartmentsModel) List(req utils.ListReq) ([]*SystemDepartments, int, error) {
	total := 0

	// 条件处理
	whereCondition := "where " + softDeleteFlag
	if req.Keyword != "" {
		whereCondition += "and `name` like '%" + req.Keyword + "%'"
	}

	orderBy := "order by sort asc"
	items := make([]*SystemDepartments, 0)
	query := fmt.Sprintf("SELECT %s FROM %s %s %s LIMIT ? OFFSET ?", systemDepartmentsRows, m.table, whereCondition, orderBy)
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

func (m *defaultSystemDepartmentsModel) ListParent(req utils.ListReq) ([]*SystemDepartments, int, error) {
	total := 0

	// 条件处理
	whereCondition := "where " + softDeleteFlag
	whereCondition += " and parent_id = 0 "

	orderBy := "order by sort asc"
	items := make([]*SystemDepartments, 0)
	query := fmt.Sprintf("SELECT %s FROM %s %s %s LIMIT ? OFFSET ?", systemDepartmentsRows, m.table, whereCondition, orderBy)
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
func (m *defaultSystemDepartmentsModel) DeleteBatch(ids string) error {
	query := fmt.Sprintf("update %s set `deleted_at`=? where `id` in (%s)", m.table, ids)
	_, err := m.ExecNoCache(query, time.Now().Format("2006-01-02 15:04:05"))

	// 删除缓存
	idArr := strings.Split(ids, ",")
	for _, v := range idArr {
		systemUserIdKey := fmt.Sprintf("%s%v", cacheSystemDepartmentsIdPrefix, v)
		m.DelCache(systemUserIdKey)
	}
	return err
}
func (m *defaultSystemDepartmentsModel) CheckDuplicate(fieldname string) (SystemDepartments, error) {
	var resp SystemDepartments
	queryString := fmt.Sprintf("select %s from %s where %s and `name` = ? limit 1", systemDepartmentsRows, m.table, softDeleteFlag)
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
