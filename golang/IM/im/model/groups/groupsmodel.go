package groups

import (
	"backend/common/response"
	"context"
	"fmt"
	"github.com/zeromicro/go-zero/core/stores/sqlx"
	"strings"
)

var _ GroupsModel = (*customGroupsModel)(nil)

type (
	// GroupsModel is an interface to be customized, add more methods here,
	// and implement the added methods in customGroupsModel.
	GroupsModel interface {
		groupsModel
		TranCreate(ctx context.Context, session sqlx.Session, groupItem *Groups) error
		TranDelete(ctx context.Context, session sqlx.Session, groupId int64) error
		FindByIds(id ...interface{}) (map[int64]Groups, error)
		FindByTitle(ctx context.Context, title string) (*Groups, error)
	}

	customGroupsModel struct {
		*defaultGroupsModel
	}
)

// NewGroupsModel returns a model for the database table.
func NewGroupsModel(conn sqlx.SqlConn) GroupsModel {
	return &customGroupsModel{
		defaultGroupsModel: newGroupsModel(conn),
	}
}

func (m *defaultGroupsModel) FindByTitle(ctx context.Context, title string) (*Groups, error) {
	query := fmt.Sprintf("select %s from %s where `title` = ? limit 1", groupsRows, m.table)
	var resp Groups
	err := m.conn.QueryRowCtx(ctx, &resp, query, title)
	if err != nil {
		return nil, err
	}
	return &resp, nil
}

func (m *defaultGroupsModel) TranCreate(ctx context.Context, session sqlx.Session, groupItem *Groups) error {
	_, err := m.FindByTitle(ctx, groupItem.Title)
	if err == nil {
		return response.Error(1000, "群组名称已存在")
	}
	query := fmt.Sprintf("insert into %s (%s) values (?, ?, ?, ?)", m.table, groupsRowsExpectAutoSet)
	execCtx, err := session.ExecCtx(ctx, query, groupItem.UserId, groupItem.Title, groupItem.Description, groupItem.ChannelId)
	if err != nil {
		return err
	}
	id, err := execCtx.LastInsertId()
	if err != nil {
		return err
	}
	groupItem.Id = id
	return nil
}

func (m *defaultGroupsModel) TranDelete(ctx context.Context, session sqlx.Session, groupId int64) error {
	query := fmt.Sprintf("delete from %s where `id` = ?", m.table)
	_, err := session.ExecCtx(ctx, query, groupId)
	return err
}

func (m *defaultGroupsModel) FindByIds(id ...interface{}) (map[int64]Groups, error) {
	if len(id) < 1 {
		return nil, fmt.Errorf("参数错误")
	}
	idStr := strings.Repeat(",?", len(id))
	query := fmt.Sprintf("select %s from %s where `id` in (%s)", groupsRows, m.table, idStr[1:])
	var resp []Groups
	rlt := make(map[int64]Groups)
	err := m.conn.QueryRows(&resp, query, id...)
	if err != nil {
		return nil, err
	} else {
		for _, v := range resp {
			rlt[v.Id] = v
		}
		return rlt, nil
	}
}
