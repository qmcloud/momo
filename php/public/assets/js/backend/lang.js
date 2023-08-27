define(['jquery', 'bootstrap', 'backend', 'table', 'form','template'], function ($, undefined, Backend, Table, Form, template) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'lang/index' + location.search,
                    add_url: 'lang/add',
                    edit_url: 'lang/edit',
                    del_url: 'lang/del',
                    multi_url: 'lang/multi',
                    table: 'lang',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'file_name', title: __('File_name')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'), searchList: {"1":__('Status 1'),"0":__('Status 0')}, formatter: Table.api.formatter.status},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            $("#c_lang_file").on("change", function () {
                var lang_file = $(this).val();
                $.ajax({
                    url: 'lang/get_lang_content',
                    type: 'get',
                    data: {
                        lang_file
                    },
                    success: function (res) {
                        if (res.code) {
                            $('dl > .form-inline').remove()
                            $("[name='row[lang_json]']").text(JSON.stringify(res.data));
                            $(document).off('change keyup changed', ".fieldlist input,.fieldlist textarea,.fieldlist select");
                            $(".fieldlist", $("form[role=form]")).off("click", ".btn-append,.append");
                            $(".fieldlist", $("form[role=form]")).off("click", ".btn-remove");
                            Form.events.fieldlist("form[role=form]");
                        }

                    }
                })
            });
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