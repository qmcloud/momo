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
	systemUsersRows                = "`avatar`,`create_by`,`created_at`,`del_flag`,`deleted_at`,`dept_id`,`email`,`id`,`login_date`,`login_ip`,`nick_name`,`password`,`phonenumber`,`remark`,`role_id`,`sex`,`status`,`update_by`,`updated_at`,`user_name`,`user_type`"
	systemUsersRowsExpectAutoSet   = "`avatar`,`create_by`,`del_flag`,`dept_id`,`email`,`login_date`,`login_ip`,`nick_name`,`password`,`phonenumber`,`remark`,`role_id`,`sex`,`status`,`update_by`,`user_name`,`user_type`"
	systemUsersRowsWithPlaceHolder = "`avatar`=?,`create_by`=?,`del_flag`=?,`dept_id`=?,`email`=?,`login_date`=?,`login_ip`=?,`nick_name`=?,`password`=?,`phonenumber`=?,`remark`=?,`role_id`=?,`sex`=?,`status`=?,`update_by`=?,`user_name`=?,`user_type`=?"

	cacheSystemUserIdPrefix = "cache#systemUser#id#"

	softDeleteFlag = "`deleted_at`='2006-01-02 15:04:05'"
)

type (
	SystemUserModel interface {
		Insert(data SystemUser) (sql.Result, error)
		FindOne(id int64) (*SystemUser, error)
		Update(data SystemUser) error
		//		UpdatePassword(data SystemUser) error
		Delete(id int64) error
		DeleteBatch(ids string) error
		FindOneByUserName(username string) (SystemUser, error)
		CheckUpdateDuplicate(username string, id int64) (SystemUser, error)
		List(req utils.ListReq) ([]*SystemUser, int, error)
	}

	defaultSystemUserModel struct {
		sqlc.CachedConn
		table string
	}

	SystemUser struct {
		Avatar      string    `db:"avatar" json:"avatar"`           // 头像地址
		CreateBy    string    `db:"create_by" json:"create_by"`     // 创建者
		CreatedAt   string    `db:"created_at" json:"created_at"`   // 创建时间
		DelFlag     int64     `db:"del_flag" json:"del_flag"`       // 删除标志（0代表存在 2代表删除）
		DeletedAt   string    `db:"deleted_at" json:"deleted_at"`   // 删除时间
		DeptId      int64     `db:"dept_id" json:"dept_id"`         // 部门ID
		Email       string    `db:"email" json:"email"`             // 用户邮箱
		Id          int64     `db:"id" json:"id"`                   // 用户ID
		LoginDate   time.Time `db:"login_date" json:"login_date"`   // 最后登录时间
		LoginIp     string    `db:"login_ip" json:"login_ip"`       // 最后登录IP
		NickName    string    `db:"nick_name" json:"nick_name"`     // 用户昵称
		Password    string    `db:"password" json:"password"`       // 密码
		Phonenumber string    `db:"phonenumber" json:"phonenumber"` // 手机号码
		Remark      string    `db:"remark" json:"remark"`           // 备注
		RoleId      int64     `db:"role_id" json:"role_id"`         // 角色ID
		Sex         int64     `db:"sex" json:"sex"`                 // 用户性别（0男 1女 2未知）
		Status      int64     `db:"status" json:"status"`           // 帐号状态（0正常 1停用）
		UpdateBy    string    `db:"update_by" json:"update_by"`     // 更新者
		UpdatedAt   string    `db:"updated_at" json:"updated_at"`   // 更新时间
		UserName    string    `db:"user_name" json:"user_name"`     // 用户账号
		UserType    int64     `db:"user_type" json:"user_type"`     // 用户类型（0系统用户）
	}
)

func NewSystemUserModel(conn sqlx.SqlConn, c cache.CacheConf) SystemUserModel {
	return &defaultSystemUserModel{
		CachedConn: sqlc.NewConn(conn, c),
		table:      "`system_users`",
	}
}

func (m *defaultSystemUserModel) List(req utils.ListReq) ([]*SystemUser, int, error) {
	//SELECT count(1) FROM `sys_apis` WHERE `sys_apis`.`deleted_at` IS NULL
	//SELECT * FROM `sys_apis` WHERE `sys_apis`.`deleted_at` IS NULL ORDER BY api_group LIMIT 10 OFFSET 30
	total := 0

	// 条件处理
	whereCondition := "where " + softDeleteFlag
	if req.Keyword != "" {
		whereCondition += "and `user_name` like '%" + req.Keyword + "%'"
	}

	orderBy := "order by id desc"
	users := make([]*SystemUser, 0)
	query := fmt.Sprintf("SELECT %s FROM %s %s %s LIMIT ? OFFSET ?", systemUsersRows, m.table, whereCondition, orderBy)
	queryCount := fmt.Sprintf("SELECT count(1) FROM %s %s", m.table, whereCondition)
	err := m.CachedConn.QueryRowNoCache(&total, queryCount)

	// 查询错误
	if err != nil {
		return users, total, err
	}

	// 没有记录
	if total == 0 {
		return users, total, nil
	}

	//获取记录
	err = m.CachedConn.QueryRowsNoCache(&users, query, req.PageSize, req.PageSize*(req.Page-1))
	if err != nil {
		logx.Errorf("usersSex.findOne error, sex=%d, err=%s", req.Page, err.Error())
		if err == sqlx.ErrNotFound {
			return nil, total, ErrNotFound
		}
		return nil, total, err
	}

	return users, total, nil
}

func (m *defaultSystemUserModel) Insert(data SystemUser) (sql.Result, error) {
	query := fmt.Sprintf("insert into %s (%s) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", m.table, systemUsersRowsExpectAutoSet)
	ret, err := m.ExecNoCache(query, data.Avatar, data.CreateBy, data.DelFlag, data.DeptId, data.Email, data.LoginDate, data.LoginIp, data.NickName, data.Password, data.Phonenumber, data.Remark, data.RoleId, data.Sex, data.Status, data.UpdateBy, data.UserName, data.UserType)

	systemUserIdKey := fmt.Sprintf("%s%v", cacheSystemUserIdPrefix, data.UserName)
	m.DelCache(systemUserIdKey)

	return ret, err
}

func (m *defaultSystemUserModel) FindOneByUserName(username string) (SystemUser, error) {
	systemUserIdKey := fmt.Sprintf("%s%v", cacheSystemUserIdPrefix, username)
	var resp SystemUser
	err := m.QueryRow(&resp, systemUserIdKey, func(conn sqlx.SqlConn, v interface{}) error {
		query := fmt.Sprintf("select %s from %s where %s and `user_name` = ? limit 1", systemUsersRows, m.table, softDeleteFlag)
		return conn.QueryRow(v, query, username)
	})
	switch err {
	case nil:
		return resp, nil
	case sqlc.ErrNotFound:
		return resp, ErrNotFound
	default:
		return resp, err
	}
}

func (m *defaultSystemUserModel) CheckUpdateDuplicate(username string, id int64) (SystemUser, error) {
	//systemUserIdKey := fmt.Sprintf("%s%v", cacheSystemUserIdPrefix, username)
	var resp SystemUser
	//err := m.QueryRow(&resp, systemUserIdKey, func(conn sqlx.SqlConn, v interface{}) error {
	//	query := fmt.Sprintf("select %s from %s where %s and `user_name` = ? and `id` != ? limit 1", systemUsersRows, m.table, softDeleteFlag)
	//	return conn.QueryRow(v, query, username, id)
	//})
	queryString := fmt.Sprintf("select %s from %s where %s and `user_name` = ? and `id` != ? limit 1", systemUsersRows, m.table, softDeleteFlag)
	err := m.CachedConn.QueryRowNoCache(&resp, queryString, username, id)
	switch err {
	case nil:
		return resp, nil
	case sqlc.ErrNotFound:
		return resp, ErrNotFound
	default:
		return resp, err
	}
}

func (m *defaultSystemUserModel) FindOne(id int64) (*SystemUser, error) {
	systemUserIdKey := fmt.Sprintf("%s%v", cacheSystemUserIdPrefix, id)
	var resp SystemUser
	err := m.QueryRow(&resp, systemUserIdKey, func(conn sqlx.SqlConn, v interface{}) error {
		query := fmt.Sprintf("select %s from %s where %s and `id` = ? limit 1", systemUsersRows, m.table, softDeleteFlag)
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

func (m *defaultSystemUserModel) Update(data SystemUser) error {
	// 根据Id查找
	systemUserIdKey := fmt.Sprintf("%s%v", cacheSystemUserIdPrefix, data.Id)
	// 根据用UserName查找
	systemUserIdKey2 := fmt.Sprintf("%s%v", cacheSystemUserIdPrefix, data.UserName)
	_, err := m.Exec(func(conn sqlx.SqlConn) (result sql.Result, err error) {
		query := fmt.Sprintf("update %s set %s where `id` = ?", m.table, systemUsersRowsWithPlaceHolder)
		return conn.Exec(query, data.Avatar, data.CreateBy, data.DelFlag, data.DeptId, data.Email, data.LoginDate, data.LoginIp, data.NickName, data.Password, data.Phonenumber, data.Remark, data.RoleId, data.Sex, data.Status, data.UpdateBy, data.UserName, data.UserType, data.Id)
	}, systemUserIdKey, systemUserIdKey2)
	return err
}

//func (m *defaultSystemUserModel) UpdatePassword(data SystemUser) error {
//	systemUserIdKey := fmt.Sprintf("%s%v", cacheSystemUserIdPrefix, data.Id)
//	systemUserIdKey2 := fmt.Sprintf("%s%v", cacheSystemUserIdPrefix, data.UserName)
//	_, err := m.Exec(func(conn sqlx.SqlConn) (result sql.Result, err error) {
//		query := fmt.Sprintf("update %s set %s where `id` = ?", m.table, "`password` = ?")
//		return conn.Exec(query, data.Password, data.Id)
//	}, systemUserIdKey, systemUserIdKey2)
//	return err
//}

func (m *defaultSystemUserModel) Delete(id int64) error {

	systemUserIdKey := fmt.Sprintf("%s%v", cacheSystemUserIdPrefix, id)
	_, err := m.Exec(func(conn sqlx.SqlConn) (result sql.Result, err error) {
		//query := fmt.Sprintf("delete from %s where `id` = ?", m.table)
		query := fmt.Sprintf("update %s set `deleted_at`=? where `id` = ?", m.table)
		return conn.Exec(query, time.Now().Format("2006-01-02 15:04:05"), id)
	}, systemUserIdKey)
	return err
}
func (m *defaultSystemUserModel) DeleteBatch(ids string) error {
	query := fmt.Sprintf("update %s set `deleted_at`=? where `id` in (%s)", m.table, ids)
	_, err := m.ExecNoCache(query, time.Now().Format("2006-01-02 15:04:05"))

	// 删除缓存
	idArr := strings.Split(ids, ",")
	for _, v := range idArr {
		systemUserIdKey := fmt.Sprintf("%s%v", cacheSystemUserIdPrefix, v)
		m.DelCache(systemUserIdKey)
	}
	return err
}

func (m *defaultSystemUserModel) formatPrimary(primary interface{}) string {
	return fmt.Sprintf("%s%v", cacheSystemUserIdPrefix, primary)
}

func (m *defaultSystemUserModel) queryPrimary(conn sqlx.SqlConn, v, primary interface{}) error {
	query := fmt.Sprintf("select %s from %s where `id` = ? limit 1", systemUsersRows, m.table)
	return conn.QueryRow(v, query, primary)
}
