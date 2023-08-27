define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'chargerule/index' + location.search,
                    add_url: 'chargerule/add',
                    edit_url: 'chargerule/edit',
                    del_url: 'chargerule/del',
                    multi_url: 'chargerule/multi',
                    import_url: 'chargerule/import',
                    table: 'charge_rules',
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
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'coin', title: __('Coin')},
                        {field: 'coin_ios', title: __('Coin_ios')},
                        {field: 'money', title: __('Money'), operate:'BETWEEN'},
                        {field: 'product_id', title: __('Product_id'), operate: 'LIKE'},
                        {field: 'give', title: __('Give')},
                        {field: 'list_order', title: __('List_order')},
                        {field: 'addtime', title: __('Addtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'coin_paypal', title: __('Coin_paypal')},
                        {field: 'type', title: __('Type')},
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
