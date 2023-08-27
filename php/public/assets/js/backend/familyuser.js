define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'familyuser/index' + location.search,
                    add_url: 'familyuser/add',
                    edit_url: 'familyuser/edit',
                    del_url: 'familyuser/del',
                    multi_url: 'familyuser/multi',
                    import_url: 'familyuser/import',
                    table: 'family_user',
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
                        {field: 'familyid', title: __('Familyid')},
                        {field: 'addtime', title: __('Addtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'uptime', title: __('Uptime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'reason', title: __('Reason'), operate: 'LIKE'},
                        {field: 'state', title: __('State')},
                        {field: 'signout', title: __('Signout')},
                        {field: 'istip', title: __('Istip')},
                        {field: 'signout_reason', title: __('Signout_reason'), operate: 'LIKE'},
                        {field: 'signout_istip', title: __('Signout_istip')},
                        {field: 'divide_family', title: __('Divide_family')},
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
