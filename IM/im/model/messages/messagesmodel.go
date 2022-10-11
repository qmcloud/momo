package messages

import (
	"fmt"
	"github.com/zeromicro/go-zero/core/stores/sqlx"
)

var _ MessagesModel = (*customMessagesModel)(nil)

type (
	// MessagesModel is an interface to be customized, add more methods here,
	// and implement the added methods in customMessagesModel.
	MessagesModel interface {
		messagesModel
		GetListByChannelId(channelId string, minId int64) ([]Messages, error)
	}

	customMessagesModel struct {
		*defaultMessagesModel
	}
)

// NewMessagesModel returns a model for the database table.
func NewMessagesModel(conn sqlx.SqlConn) MessagesModel {
	return &customMessagesModel{
		defaultMessagesModel: newMessagesModel(conn),
	}
}

func (m *customMessagesModel) GetListByChannelId(channelId string, minId int64) ([]Messages, error) {
	var resp []Messages
	idWhere := ""
	if minId > 0 {
		idWhere = fmt.Sprintf("`id` < %d", minId)
	}
	query := fmt.Sprintf("select %s from %s where `channel_id` = ? %s order by id desc limit 20", messagesRows, m.table, idWhere)
	err := m.conn.QueryRows(&resp, query, channelId)
	switch err {
	case nil:
		return resp, nil
	default:
		return nil, err

	}
}
