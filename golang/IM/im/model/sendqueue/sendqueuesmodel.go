package sendqueue

import (
	"context"
	"fmt"
	"github.com/zeromicro/go-zero/core/stores/sqlx"
)

var _ SendQueuesModel = (*customSendQueuesModel)(nil)

type (
	// SendQueuesModel is an interface to be customized, add more methods here,
	// and implement the added methods in customSendQueuesModel.
	SendQueuesModel interface {
		sendQueuesModel
		FindByUserId(ctx context.Context, userId int64) ([]SendQueues, error)
	}

	customSendQueuesModel struct {
		*defaultSendQueuesModel
	}
)

// NewSendQueuesModel returns a model for the database table.
func NewSendQueuesModel(conn sqlx.SqlConn) SendQueuesModel {
	return &customSendQueuesModel{
		defaultSendQueuesModel: newSendQueuesModel(conn),
	}
}

func (m customSendQueuesModel) FindByUserId(ctx context.Context, userId int64) ([]SendQueues, error) {
	var resp []SendQueues
	query := fmt.Sprintf("select %s from %s where `user_id` = ?", sendQueuesRows, m.table)
	err := m.conn.QueryRowsCtx(ctx, &resp, query, userId)
	switch err {
	case nil:
		return resp, nil
	default:
		return nil, err
	}
}
