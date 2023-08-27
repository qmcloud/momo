define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'gift/index' + location.search,
                    add_url: 'gift/add',
                    edit_url: 'gift/edit',
                    del_url: 'gift/del',
                    multi_url: 'gift/multi',
                    import_url: 'gift/import',
                    table: 'gift',
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
                        {field: 'mark', title: __('Mark')},
                        {field: 'type', title: __('Type')},
                        {field: 'giftname', title: __('Giftname'), operate: 'LIKE'},
                        {field: 'needcoin', title: __('Needcoin')},
                        {field: 'gifticon', title: __('Gifticon'), operate: 'LIKE', formatter: Table.api.formatter.icon},
                        {field: 'list_order', title: __('List_order')},
                        {field: 'addtime', title: __('Addtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'swftype', title: __('Swftype')},
                        {field: 'swf', title: __('Swf'), operate: 'LIKE'},
                        {field: 'swftime', title: __('Swftime'), operate:'BETWEEN'},
                        {field: 'isplatgift', title: __('Isplatgift')},
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
