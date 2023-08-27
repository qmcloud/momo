define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'dynamic/index' + location.search,
                    add_url: 'dynamic/add',
                    edit_url: 'dynamic/edit',
                    del_url: 'dynamic/del',
                    multi_url: 'dynamic/multi',
                    import_url: 'dynamic/import',
                    table: 'dynamic',
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
                        {field: 'video_thumb', title: __('Video_thumb'), operate: 'LIKE'},
                        {field: 'href', title: __('Href'), operate: 'LIKE'},
                        {field: 'voice', title: __('Voice'), operate: 'LIKE'},
                        {field: 'length', title: __('Length')},
                        {field: 'likes', title: __('Likes')},
                        {field: 'comments', title: __('Comments')},
                        {field: 'type', title: __('Type')},
                        {field: 'isdel', title: __('Isdel')},
                        {field: 'status', title: __('Status')},
                        {field: 'uptime', title: __('Uptime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'xiajia_reason', title: __('Xiajia_reason'), operate: 'LIKE'},
                        {field: 'lat', title: __('Lat'), operate: 'LIKE'},
                        {field: 'lng', title: __('Lng'), operate: 'LIKE'},
                        {field: 'city', title: __('City'), operate: 'LIKE'},
                        {field: 'address', title: __('Address'), operate: 'LIKE'},
                        {field: 'addtime', title: __('Addtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'fail_reason', title: __('Fail_reason'), operate: 'LIKE'},
                        {field: 'show_val', title: __('Show_val')},
                        {field: 'recommend_val', title: __('Recommend_val')},
                        {field: 'labelid', title: __('Labelid')},
                        {field: 'dynamicid', title: __('Dynamicid'), operate: 'LIKE'},
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
