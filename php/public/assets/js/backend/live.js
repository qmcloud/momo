define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'live/index' + location.search,
                    add_url: 'live/add',
                    edit_url: 'live/edit',
                    del_url: 'live/del',
                    multi_url: 'live/multi',
                    import_url: 'live/import',
                    table: 'live',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'uid',
                sortName: 'uid',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'uid', title: __('Uid')},
                        {field: 'showid', title: __('Showid')},
                        {field: 'islive', title: __('Islive')},
                        {field: 'starttime', title: __('Starttime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'title', title: __('Title'), operate: 'LIKE'},
                        {field: 'user_nicename', title: __('User_nicename'), operate: 'LIKE',formatter: Table.api.formatter.label},
                        {field: 'province', title: __('Province'), operate: 'LIKE'},
                        {field: 'stream', title: __('Stream'), operate: 'LIKE'},
                        {field: 'thumb', title: __('Thumb'),operate: 'LIKE',events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'pull', title: __('Pull'), operate: 'LIKE'},
                        {field: 'lng', title: __('Lng'), operate: 'LIKE'},
                        {field: 'lat', title: __('Lat'), operate: 'LIKE'},
                        {field: 'type_val', title: __('Type_val'), operate: 'LIKE'},
                        {field: 'isvideo', title: __('Isvideo'),formatter: Table.api.formatter.status,searchList: {0: __('No'), 1: __('Yes')}},
                        {field: 'wy_cid', title: __('Wy_cid'), operate: 'LIKE'},
                        {field: 'goodnum', title: __('Goodnum'), operate: 'LIKE',formatter: Table.api.formatter.label},
                        {field: 'anyway', title: __('Anyway'),formatter: Table.api.formatter.status,searchList: {0: __('Hor'), 1: __('Per')}},
                        {field: 'liveclassid', title: __('Liveclassid')},
                        {field: 'hotvotes', title: __('Hotvotes')},
                        {field: 'pkuid', title: __('Pkuid')},
                        {field: 'pkstream', title: __('Pkstream'), operate: 'LIKE'},
                        {field: 'ismic', title: __('Ismic'),formatter: Table.api.formatter.status,searchList: {0: __('No'), 1: __('Yes')}},
                        {field: 'ishot', title: __('Ishot'),formatter: Table.api.formatter.status,searchList: {0: __('No'), 1: __('Yes')}},
                        {field: 'isrecommend', title: __('Isrecommend'),formatter: Table.api.formatter.status,searchList: {0: __('No'), 1: __('Yes')}},
                        {field: 'deviceinfo', title: __('Deviceinfo'), operate: 'LIKE'},
                        {field: 'isshop', title: __('Isshop'),formatter: Table.api.formatter.status,searchList: {0: __('No'), 1: __('Yes')}},
                        {field: 'game_action', title: __('Game_action')},
                        {field: 'banker_coin', title: __('Banker_coin')},
                        {field: 'isoff', title: __('Isoff'),formatter: Table.api.formatter.status,searchList: {0: __('No'), 1: __('Yes')}},
                        {field: 'offtime', title: __('Offtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'recommend_time', title: __('Recommend_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'live_type', title: __('Live_type'),formatter: Table.api.formatter.status,searchList: {0: __('Live'), 1: __('Voice')}},

                        {field: 'avatar', title: __('Avatar'), operate: 'LIKE', events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'look_users', title: __('Look_users'), operate: 'LIKE'},
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
