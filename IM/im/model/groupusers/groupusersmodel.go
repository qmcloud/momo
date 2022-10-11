package groupusers

import (
	"context"
	"fmt"
	"github.com/zeromicro/go-zero/core/stores/sqlx"
)

var _ GroupUsersModel = (*customGroupUsersModel)(nil)

type (
	// GroupUsersModel is an interface to be customized, add more methods here,
	// and implement the added methods in customGroupUsersModel.
	GroupUsersModel interface {
		groupUsersModel
		TranCreate(ctx context.Context, session sqlx.Session, groupUserItem *GroupUsers) error
		TranDeleteByGroupId(ctx context.Context, session sqlx.Session, groupId int64) error
		IsInGroup(ctx context.Context, id, userId int64) (*GroupUsers, error)
		InGroups(ctx context.Context, id int64) ([]GroupUsers, error)
		FindUsersByChannelId(channelId string) ([]GroupUsers, error)
	}

	customGroupUsersModel struct {
		*defaultGroupUsersModel
	}
)

// NewGroupUsersModel returns a model for the database table.
func NewGroupUsersModel(conn sqlx.SqlConn) GroupUsersModel {
	return &customGroupUsersModel{
		defaultGroupUsersModel: newGroupUsersModel(conn),
	}
}

func (c *customGroupUsersModel) TranCreate(ctx context.Context, session sqlx.Session, data *GroupUsers) error {
	query := fmt.Sprintf("insert into %s (%s) values (?, ?, ?, ?)", c.table, groupUsersRowsExpectAutoSet)
	_, err := session.ExecCtx(ctx, query, data.GroupId, data.UserId, data.ChannelId, data.IsManager)
	if err != nil {
		return err
	}
	return nil
}

func (c *customGroupUsersModel) TranDeleteByGroupId(ctx context.Context, session sqlx.Session, groupId int64) error {
	query := fmt.Sprintf("delete from %s where `group_id` = ?", c.table)
	_, err := session.ExecCtx(ctx, query, groupId)
	return err
}

func (c *customGroupUsersModel) IsInGroup(ctx context.Context, id, userId int64) (*GroupUsers, error) {
	query := fmt.Sprintf("select %s from %s where `group_id` = ? and user_id = ?", groupUsersRows, c.table)
	var u GroupUsers
	err := c.conn.QueryRowCtx(ctx, &u, query, id, userId)
	if err != nil {
		return nil, err
	}
	return &u, nil
}

func (c *customGroupUsersModel) InGroups(ctx context.Context, id int64) ([]GroupUsers, error) {
	query := fmt.Sprintf("select %s from %s where user_id = ?", groupUsersRows, c.table)
	var u []GroupUsers
	err := c.conn.QueryRowsCtx(ctx, &u, query, id)
	if err == nil {
		return u, nil
	}
	return nil, err
}

func (c *customGroupUsersModel) FindUsersByChannelId(channelId string) ([]GroupUsers, error) {
	query := fmt.Sprintf("select %s from %s where channel_id = ?", groupUsersRows, c.table)
	var u []GroupUsers
	err := c.conn.QueryRowsCtx(context.Background(), &u, query, channelId)
	if err == nil {
		return u, nil
	}
	return nil, err
}
