package user

import (
	"backend/service/im/model/hasusers"
	"context"
	"fmt"
	"github.com/zeromicro/go-zero/core/stores/cache"
	"github.com/zeromicro/go-zero/core/stores/sqlc"
	"github.com/zeromicro/go-zero/core/stores/sqlx"
	"strings"
)

var _ UsersModel = (*customUsersModel)(nil)

type (
	// UsersModel is an interface to be customized, add more methods here,
	// and implement the added methods in customUsersModel.
	UsersModel interface {
		usersModel
		FindByIds(ctx context.Context, ids ...interface{}) (map[int64]Users, error)
		FindRawByName(ctx context.Context, userName string) (*Users, error)
		Friends(hasUserModel hasusers.UserUsersModel, userId int64) ([]Users, error)
		GetListByKeyword(keyword string, id int64) ([]Users, error)
	}

	customUsersModel struct {
		*defaultUsersModel
		sqlWhere string
	}
)

// NewUsersModel returns a model for the database table.
func NewUsersModel(conn sqlx.SqlConn, c cache.CacheConf) UsersModel {
	return &customUsersModel{
		defaultUsersModel: newUsersModel(conn, c),
	}
}

func (m *customUsersModel) FindByIds(ctx context.Context, ids ...interface{}) (map[int64]Users, error) {
	if len(ids) < 1 {
		return nil, fmt.Errorf("参数ids必传")
	}
	mapList := make(map[int64]Users)
	var resp []Users
	whereIn := strings.Repeat(",?", len(ids))
	query := fmt.Sprintf("select %s from %s where `id` in (%s)", usersRows, m.table, whereIn[1:])
	err := m.QueryRowsNoCache(&resp, query, ids...)
	switch err {
	case nil:
		for _, v := range resp {
			mapList[v.Id] = v
		}
		return mapList, nil
	case sqlc.ErrNotFound:
		return nil, ErrNotFound
	default:
		return nil, err
	}
}

func (m *customUsersModel) FindRawByName(ctx context.Context, userName string) (*Users, error) {
	var resp Users
	err := m.QueryRowNoCache(&resp, fmt.Sprintf("select %s from %s where `user_name` = ? limit 1", usersRows, m.table), userName)
	switch err {
	case nil:
		return &resp, nil
	case sqlc.ErrNotFound:
		return nil, ErrNotFound
	default:
		return nil, err
	}
}

func (m *customUsersModel) Friends(hasUserModel hasusers.UserUsersModel, userId int64) ([]Users, error) {
	var resp []Users
	rows := "users.`id`, users.`user_name`, users.`nick_name`, users.`password`, users.`mobile`, users.`create_time`, users.`update_time`"
	query := fmt.Sprintf("select %s from %s join user_users on user_users.`has_user_id` = users.`id` where user_users.`user_id` = ?", rows, m.table)
	err := m.QueryRowsNoCacheCtx(context.Background(), &resp, query, userId)
	switch err {
	case nil:
		return resp, nil
	case sqlc.ErrNotFound:
		return resp, ErrNotFound
	default:
		return nil, err
	}
}

func (m *customUsersModel) GetListByKeyword(keyword string, id int64) ([]Users, error) {
	var resp []Users
	var err error
	if keyword != "" {
		query := fmt.Sprintf("select %s from %s where `nick_name` like ? and id != ? limit 20", usersRows, m.table)
		fmt.Println(query)
		err = m.QueryRowsNoCacheCtx(context.Background(), &resp, query, keyword+"%", id)
	} else {
		query := fmt.Sprintf("select %s from %s where id != ? limit 20", usersRows, m.table)
		err = m.QueryRowsNoCacheCtx(context.Background(), &resp, query, id)
	}
	switch err {
	case nil:
		return resp, nil
	case sqlc.ErrNotFound:
		return resp, ErrNotFound
	default:
		return nil, err
	}
}
