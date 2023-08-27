define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'red/index' + location.search,
                    add_url: 'red/add',
                    edit_url: 'red/edit',
                    del_url: 'red/del',
                    multi_url: 'red/multi',
                    import_url: 'red/import',
                    table: 'red',
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
                        {field: 'showid', title: __('Showid')},
                        {field: 'uid', title: __('Uid')},
                        {field: 'liveuid', title: __('Liveuid')},
                        {field: 'type', title: __('Type')},
                        {field: 'type_grant', title: __('Type_grant')},
                        {field: 'coin', title: __('Coin')},
                        {field: 'nums', title: __('Nums')},
                        {field: 'des', title: __('Des'), operate: 'LIKE'},
                        {field: 'effecttime', title: __('Effecttime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'addtime', title: __('Addtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status')},
                        {field: 'coin_rob', title: __('Coin_rob')},
                        {field: 'nums_rob', title: __('Nums_rob')},
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
