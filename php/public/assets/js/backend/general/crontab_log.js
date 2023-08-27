define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'general/crontab_log/index',
                    add_url: 'general/crontab_log/add',
                    edit_url: '',
                    del_url: 'general/crontab_log/del',
                    multi_url: 'general/crontab_log/multi',
                    table: 'crontab'
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                sortName: 'id',
                columns: [
                    [
                        {field: 'state', checkbox: true,},
                        {field: 'id', title: 'ID'},
                        {field: 'crontab_id', title: __('Crontab_id'), formatter: Table.api.formatter.search},
                        {field: 'executetime', title: __('Execute time'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        {field: 'completetime', title: __('Complete time'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        {field: 'status', title: __('Status'), searchList: Config.statusList, custom: {success: 'success', failure: 'danger'}, formatter: Table.api.formatter.status},
                        {
                            field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                            buttons: [
                                {
                                    name: "detail",
                                    text: __("Result"),
                                    classname: "btn btn-xs btn-info btn-dialog",
                                    icon: "fa fa-file",
                                    url: "general/crontab_log/detail",
                                    extend: "data-window='parent'"
                                }
                            ]
                        }
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

            },
        }
    };
    return Controller;
});