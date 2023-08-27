define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'idcard/index' + location.search,
                    add_url: 'idcard/add',
                    edit_url: 'idcard/edit',
                    del_url: 'idcard/del',
                    multi_url: 'idcard/multi',
                    import_url: 'idcard/import',
                    table: 'idcard',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'uid',
                sortName: 'uid',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'uid', title: __('Uid')},
                        {field: 'real_name', title: __('Real_name'), operate: 'LIKE'},
                        {field: 'mobile', title: __('Mobile'), operate: 'LIKE'},
                        {field: 'cer_no', title: __('Cer_no'), operate: 'LIKE'},
                        {field: 'front_view', title: __('Front_view'),events: Table.api.events.image, formatter: Table.api.formatter.image,operate: 'LIKE'},
                        {field: 'back_view', title: __('Back_view'),events: Table.api.events.image, formatter: Table.api.formatter.image,operate: 'LIKE'},
                        {field: 'handset_view', title: __('Handset_view'),events: Table.api.events.image, formatter: Table.api.formatter.image, operate: 'LIKE'},
                        {field: 'addtime', title: __('Addtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'uptime', title: __('Uptime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'),formatter: Table.api.formatter.status, searchList: {0: __('Doing'), 1: __('Success'), 2: __('Fail')}},
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
