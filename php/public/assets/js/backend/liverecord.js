define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'liverecord/index' + location.search,
                    add_url: 'liverecord/add',
                    edit_url: 'liverecord/edit',
                    del_url: 'liverecord/del',
                    multi_url: 'liverecord/multi',
                    import_url: 'liverecord/import',
                    table: 'live_record',
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
                        {field: 'showid', title: __('Showid')},
                        {field: 'nums', title: __('Nums')},
                        {field: 'starttime', title: __('Starttime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'endtime', title: __('Endtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'title', title: __('Title'), operate: 'LIKE'},
                        {field: 'province', title: __('Province'), operate: 'LIKE'},
                        {field: 'city', title: __('City'), operate: 'LIKE'},
                        {field: 'stream', title: __('Stream'), operate: 'LIKE'},
                        {field: 'thumb', title: __('Thumb'), operate: 'LIKE'},
                        {field: 'lng', title: __('Lng'), operate: 'LIKE'},
                        {field: 'lat', title: __('Lat'), operate: 'LIKE'},
                        {field: 'type', title: __('Type')},
                        {field: 'type_val', title: __('Type_val'), operate: 'LIKE'},
                        {field: 'votes', title: __('Votes'), operate: 'LIKE'},
                        {field: 'time', title: __('Time'), operate: 'LIKE'},
                        {field: 'liveclassid', title: __('Liveclassid')},
                        {field: 'video_url', title: __('Video_url'), operate: 'LIKE', formatter: Table.api.formatter.url},
                        {field: 'live_type', title: __('Live_type')},
                        {field: 'deviceinfo', title: __('Deviceinfo'), operate: 'LIKE'},
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
