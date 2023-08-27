define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'scorerecord/index' + location.search,
                    add_url: 'scorerecord/add',
                    edit_url: 'scorerecord/edit',
                    del_url: 'scorerecord/del',
                    multi_url: 'scorerecord/multi',
                    import_url: 'scorerecord/import',
                    table: 'user_scorerecord',
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
                        {field: 'type', title: __('Type')},
                        {field: 'action', title: __('Action')},
                        {field: 'uid', title: __('Uid')},
                        {field: 'touid', title: __('Touid')},
                        {field: 'giftid', title: __('Giftid')},
                        {field: 'giftcount', title: __('Giftcount')},
                        {field: 'totalcoin', title: __('Totalcoin')},
                        {field: 'showid', title: __('Showid')},
                        {field: 'addtime', title: __('Addtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'game_action', title: __('Game_action')},
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
