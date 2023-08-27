define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'level/index' + location.search,
                    add_url: 'level/add',
                    edit_url: 'level/edit',
                    del_url: 'level/del',
                    multi_url: 'level/multi',
                    import_url: 'level/import',
                    table: 'level',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'levelid', title: __('Levelid')},
                        {field: 'levelname', title: __('Levelname'), operate: 'LIKE'},
                        {field: 'level_up', title: __('Level_up')},
                        {field: 'addtime', title: __('Addtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'thumb', title: __('Thumb'), operate: 'LIKE'},
                        {field: 'colour', title: __('Colour'), operate: 'LIKE'},
                        {field: 'thumb_mark', title: __('Thumb_mark'), operate: 'LIKE'},
                        {field: 'bg', title: __('Bg'), operate: 'LIKE'},
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
