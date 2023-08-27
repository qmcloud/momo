package notices

import (
	"context"
	"database/sql"
	"fmt"
	"github.com/zeromicro/go-zero/core/stores/sqlx"
	"time"
)

var _ NoticesModel = (*customNoticesModel)(nil)

const (
	FRIEND = 1
	GROUP  = 2
)

type (
	// NoticesModel is an interface to be customized, add more methods here,
	// and implement the added methods in customNoticesModel.
	NoticesModel interface {
		noticesModel
		CheckSendAddFriend(userId, friendId int64) (*Notices, error)
		GetListByUserId(userId int64) ([]ListItem, error)
		TransInsert(ctx context.Context, session sqlx.Session, data *Notices) (sql.Result, error)
		AddFriend(userId, friendId int64, userNickName, friendNickName string) (*Notices, error)
	}

	customNoticesModel struct {
		*defaultNoticesModel
	}
)

type (
	ListItem struct {
		Id         int64     `db:"id" json:"id"`
		Tp         int64     `db:"type" json:"type"`
		IsAgree    string    `db:"is_agree" json:"is_agree"`
		NickName   string    `db:"nick_name" json:"nick_name"`
		Content    string    `db:"content" json:"content"`
		CreateTime time.Time `db:"create_time" json:"create_time"`
		Status     int64     `db:"status" json:"status"`
	}
)

// NewNoticesModel returns a model for the database table.
func NewNoticesModel(conn sqlx.SqlConn) NoticesModel {
	return &customNoticesModel{
		defaultNoticesModel: newNoticesModel(conn),
	}
}

func (m *customNoticesModel) CheckSendAddFriend(userId, friendId int64) (*Notices, error) {
	var resp Notices
	query := fmt.Sprintf("select %s from %s where `pub_user_id` = ? and `sub_user_id` = ? and status = 0 limit 1", noticesRows, m.table)
	err := m.conn.QueryRow(&resp, query, userId, friendId)
	switch err {
	case ErrNotFound:
		return nil, nil
	case nil:
		return &resp, nil
	default:
		return nil, err
	}
}

func (m *customNoticesModel) GetListByUserId(userId int64) ([]ListItem, error) {
	var resp []ListItem
	selectRows := "notices.`id`,notices.`type`,notices.`content`,notices.`is_agree`,notices.`create_time`,notices.`status`,users.`nick_name`"
	query := fmt.Sprintf("select %s from %s join users on %s.pub_user_id = users.id where `sub_user_id` = ? order by id desc limit 20", selectRows, m.table, m.table)
	err := m.conn.QueryRows(&resp, query, userId)
	switch err {
	case nil:
		return resp, nil
	default:
		return nil, err
	}
}

func (m *defaultNoticesModel) TransInsert(ctx context.Context, session sqlx.Session, data *Notices) (sql.Result, error) {
	query := fmt.Sprintf("insert into %s (%s) values (?, ?, ?, ?, ?, ?, ?)", m.table, noticesRowsExpectAutoSet)
	ret, err := session.ExecCtx(ctx, query, data.Tp, data.PubUserId, data.SubUserId, data.LinkId, data.Content, data.IsAgree, data.Status)
	return ret, err
}

func (m *customNoticesModel) AddFriend(userId, friendId int64, userNickName, friendNickName string) (*Notices, error) {
	//linkAdd := Notices{PubUserId: userId, SubUserId: userId, Tp: 1, Content: fmt.Sprintf("你请求添加%s为好友", friendNickName), IsAgree: "未处理", Status: 1, CreateTime: time.Now()}
	noticeAdd := Notices{PubUserId: userId, SubUserId: friendId, Tp: 1, Content: fmt.Sprintf("%s请求添加您为好友", userNickName), CreateTime: time.Now()}
	err := m.conn.Transact(func(session sqlx.Session) error {
		//link, _ := m.TransInsert(context.Background(), session, &linkAdd)
		//linkId, _ := link.LastInsertId()
		//linkAdd.Id = linkId
		//noticeAdd.LinkId = linkId
		notice, _ := m.TransInsert(context.Background(), session, &noticeAdd)
		noticeId, _ := notice.LastInsertId()
		noticeAdd.Id = noticeId
		return nil
	})
	if err != nil {
		return nil, fmt.Errorf("添加好友申请失败：%s", err.Error())
	}
	return &noticeAdd, nil
}
