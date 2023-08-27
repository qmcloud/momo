define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'chargeuser/index' + location.search,
                    add_url: 'chargeuser/add',
                    edit_url: 'chargeuser/edit',
                    del_url: 'chargeuser/del',
                    multi_url: 'chargeuser/multi',
                    import_url: 'chargeuser/import',
                    table: 'charge_user',
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
                        {field: 'touid', title: __('Touid')},
                        {field: 'money', title: __('Money'), operate:'BETWEEN'},
                        {field: 'coin', title: __('Coin')},
                        {field: 'coin_give', title: __('Coin_give')},
                        {field: 'orderno', title: __('Orderno'), operate: 'LIKE'},
                        {field: 'trade_no', title: __('Trade_no'), operate: 'LIKE'},
                        {field: 'status', title: __('Status')},
                        {field: 'addtime', title: __('Addtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'type', title: __('Type')},
                        {field: 'ambient', title: __('Ambient')},
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
