define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'portal/index' + location.search,
                    add_url: 'portal/add',
                    edit_url: 'portal/edit',
                    del_url: 'portal/del',
                    multi_url: 'portal/multi',
                    import_url: 'portal/import',
                    table: 'portal_post',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'parent_id', title: __('Parent_id')},
                        {field: 'post_type', title: __('Post_type')},
                        {field: 'post_format', title: __('Post_format')},
                        {field: 'user_id', title: __('User_id')},
                        {field: 'post_status', title: __('Post_status')},
                        {field: 'comment_status', title: __('Comment_status')},
                        {field: 'is_top', title: __('Is_top')},
                        {field: 'recommended', title: __('Recommended')},
                        {field: 'post_hits', title: __('Post_hits')},
                        {field: 'post_favorites', title: __('Post_favorites')},
                        {field: 'post_like', title: __('Post_like')},
                        {field: 'comment_count', title: __('Comment_count')},
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'update_time', title: __('Update_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'published_time', title: __('Published_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'delete_time', title: __('Delete_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'post_title', title: __('Post_title'), operate: 'LIKE'},
                        {field: 'post_keywords', title: __('Post_keywords'), operate: 'LIKE'},
                        {field: 'post_excerpt', title: __('Post_excerpt'), operate: 'LIKE'},
                        {field: 'post_source', title: __('Post_source'), operate: 'LIKE'},
                        {field: 'thumbnail', title: __('Thumbnail'), operate: 'LIKE'},
                        {field: 'post_content', title: __('Post_content')},
                        {field: 'type', title: __('Type')},
                        {field: 'list_order', title: __('List_order')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
