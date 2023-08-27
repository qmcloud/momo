define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'video/index' + location.search,
                    add_url: 'video/add',
                    edit_url: 'video/edit',
                    del_url: 'video/del',
                    multi_url: 'video/multi',
                    import_url: 'video/import',
                    table: 'video',
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
                        {field: 'uid', title: __('Uid')},
                        {field: 'title', title: __('Title'), operate: 'LIKE'},
                        {field: 'thumb', title: __('Thumb'), operate: 'LIKE'},
                        {field: 'thumb_s', title: __('Thumb_s'), operate: 'LIKE'},
                        {field: 'href', title: __('Href'), operate: 'LIKE'},
                        {field: 'href_w', title: __('Href_w'), operate: 'LIKE'},
                        {field: 'likes', title: __('Likes')},
                        {field: 'views', title: __('Views')},
                        {field: 'comments', title: __('Comments')},
                        {field: 'steps', title: __('Steps')},
                        {field: 'shares', title: __('Shares')},
                        {field: 'addtime', title: __('Addtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'lat', title: __('Lat'), operate: 'LIKE'},
                        {field: 'lng', title: __('Lng'), operate: 'LIKE'},
                        {field: 'city', title: __('City'), operate: 'LIKE'},
                        {field: 'isdel', title: __('Isdel')},
                        {field: 'status', title: __('Status')},
                        {field: 'music_id', title: __('Music_id')},
                        {field: 'xiajia_reason', title: __('Xiajia_reason'), operate: 'LIKE'},
                        {field: 'nopass_time', title: __('Nopass_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'watch_ok', title: __('Watch_ok')},
                        {field: 'is_ad', title: __('Is_ad')},
                        {field: 'ad_endtime', title: __('Ad_endtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'ad_url', title: __('Ad_url'), operate: 'LIKE', formatter: Table.api.formatter.url},
                        {field: 'orderno', title: __('Orderno')},
                        {field: 'type', title: __('Type')},
                        {field: 'goodsid', title: __('Goodsid')},
                        {field: 'classid', title: __('Classid')},
                        {field: 'anyway', title: __('Anyway'), operate: 'LIKE'},
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
