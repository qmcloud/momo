define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init();
            this.table.first();
            this.table.second();
        },
        table: {
            first: function () {
                // 表格1
                var table1 = $("#table1");
                table1.bootstrapTable({
                    url: 'example/tablelink/table1',
                    toolbar: '#toolbar1',
                    sortName: 'id',
                    search: false,
                    columns: [
                        [
                            // {field: 'state', checkbox: true,},
                            {field: 'id', title: 'ID'},
                            {field: 'username', title: __('Nickname')},
                            {
                                field: 'operate', title: __('Operate'), table: table1, events: Table.api.events.operate, buttons: [
                                    {
                                        name: 'log',
                                        title: '日志列表',
                                        text: '日志列表',
                                        icon: 'fa fa-list',
                                        classname: 'btn btn-primary btn-xs btn-click',
                                        click: function (e, data) {
                                            $("#myTabContent2 .form-commonsearch input[name='username']").val(data.username);
                                            $("#myTabContent2 .btn-refresh").trigger("click");
                                        }
                                    }
                                ], formatter: Table.api.formatter.operate
                            }
                        ]
                    ]
                });

                // 为表格1绑定事件
                Table.api.bindevent(table1);
            },
            second: function () {
                // 表格2
                var table2 = $("#table2");
                table2.bootstrapTable({
                    url: 'example/tablelink/table2',
                    extend: {
                        index_url: '',
                        add_url: '',
                        edit_url: '',
                        del_url: '',
                        multi_url: '',
                        table: '',
                    },
                    toolbar: '#toolbar2',
                    sortName: 'id',
                    search: false,
                    columns: [
                        [
                            {field: 'state', checkbox: true,},
                            {field: 'id', title: 'ID'},
                            {field: 'username', title: __('Nickname')},
                            {field: 'title', title: __('Title')},
                            {field: 'url', title: __('Url'), align: 'left', formatter: Table.api.formatter.url},
                            {field: 'ip', title: __('ip')},
                            {field: 'createtime', title: __('Createtime'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        ]
                    ]
                });

                // 为表格2绑定事件
                Table.api.bindevent(table2);
            }
        },
    };
    return Controller;
});
