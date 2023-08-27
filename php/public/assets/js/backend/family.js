define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'family/index' + location.search,
                    add_url: 'family/add',
                    edit_url: 'family/edit',
                    del_url: 'family/del',
                    multi_url: 'family/multi',
                    import_url: 'family/import',
                    table: 'family',
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
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'badge', title: __('Badge'), operate: 'LIKE'},
                        {field: 'apply_pos', title: __('Apply_pos'), operate: 'LIKE'},
                        {field: 'apply_side', title: __('Apply_side'), operate: 'LIKE'},
                        {field: 'carded', title: __('Carded'), operate: 'LIKE'},
                        {field: 'fullname', title: __('Fullname'), operate: 'LIKE'},
                        {field: 'addtime', title: __('Addtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'state', title: __('State')},
                        {field: 'reason', title: __('Reason'), operate: 'LIKE'},
                        {field: 'disable', title: __('Disable')},
                        {field: 'divide_family', title: __('Divide_family')},
                        {field: 'istip', title: __('Istip')},
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
