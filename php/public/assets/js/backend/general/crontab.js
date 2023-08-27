define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'general/crontab/index',
                    add_url: 'general/crontab/add',
                    edit_url: 'general/crontab/edit',
                    del_url: 'general/crontab/del',
                    multi_url: 'general/crontab/multi',
                    table: 'crontab'
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                sortName: 'weigh',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {field: 'state', checkbox: true,},
                        {field: 'id', title: 'ID'},
                        {field: 'type', title: __('Type'), searchList: Config.typeList, formatter: Table.api.formatter.label, custom: {sql: 'warning', url: 'info', shell: 'success'}},
                        {field: 'title', title: __('Title')},
                        {field: 'maximums', title: __('Maximums'), formatter: Controller.api.formatter.maximums},
                        {field: 'executes', title: __('Executes')},
                        {field: 'begintime', title: __('Begin time'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange'},
                        {field: 'endtime', title: __('End time'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange'},
                        {field: 'nexttime', title: __('Next execute time'), formatter: Controller.api.formatter.nexttime, operate: false, addclass: 'datetimerange', sortable: true},
                        {field: 'executetime', title: __('Execute time'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        {field: 'weigh', title: __('Weigh')},
                        {field: 'status', title: __('Status'), searchList: {"normal": __('Normal'), "hidden": __('Hidden'), "expired": __('Expired'), "completed": __('Completed')}, formatter: Table.api.formatter.status},
                        {
                            field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                            buttons: [
                                {
                                    name: "detail",
                                    icon: "fa fa-list",
                                    title: function (row, index) {
                                        return __('Logs') + "[" + row['title'] + "]";
                                    },
                                    text: __('Logs'),
                                    classname: "btn btn-xs btn-info btn-dialog",
                                    url: "general/crontab_log/index?crontab_id={ids}",
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
                $('#schedule').on('valid.field', function (e, result) {
                    $("#pickdays").trigger("change");
                });
                Form.api.bindevent($("form[role=form]"));
                $(document).on("change", "#pickdays", function () {
                    Fast.api.ajax({url: "general/crontab/get_schedule_future", data: {schedule: $("#schedule").val(), days: $(this).val()}}, function (data, ret) {
                        if (typeof data.futuretime !== 'undefined' && $.isArray(data.futuretime)) {
                            var result = [];
                            $.each(data.futuretime, function (i, j) {
                                result.push("<li class='list-group-item'>" + j + "<span class='badge'>" + (i + 1) + "</span></li>");
                            });
                            $("#scheduleresult").html(result.join(""));
                        } else {
                            $("#scheduleresult").html("");
                        }
                        return false;
                    });
                });
                $("#pickdays").trigger("change");
            },
            formatter: {
                nexttime: function (value, row, index) {
                    if (isNaN(value)) {
                        return value;
                    } else {
                        return Table.api.formatter.datetime.call(this, value, row, index);
                    }
                },
                maximums: function (value, row, index) {
                    return value === 0 ? __('No limit') : value;
                }
            }
        }
    };
    return Controller;
});
