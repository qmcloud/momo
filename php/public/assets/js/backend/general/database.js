define(['jquery', 'bootstrap', 'backend', 'template'], function ($, undefined, Backend, Template) {

    var Controller = {
        index: function () {

            //如果有备份权限
            if ($("#backuplist").size() > 0) {
                Fast.api.ajax({
                    url: "general/database/backuplist",
                    type: 'get'
                }, function (data) {
                    $("#backuplist").html(Template("backuptpl", {backuplist: data.backuplist}));
                    return false;
                });
                return false;
            }

            //禁止在操作select元素时关闭dropdown的关闭事件
            $("#database").on('click', '.dropdown-menu input, .dropdown-menu label, .dropdown-menu select', function (e) {
                e.stopPropagation();
            });

            //提交时检查是否有删除或清空操作
            $("#database").on("submit", "#sqlexecute", function () {
                var v = $("#sqlquery").val().toLowerCase();
                if ((v.indexOf("delete ") >= 0 || v.indexOf("truncate ") >= 0) && !confirm(__('Are you sure you want to delete or turncate?'))) {
                    return false;
                }
            });

            //事件按钮操作
            $("#database").on("click", "ul#subaction li input", function () {
                $("#topaction").val($(this).attr("rel"));
                return true;
            });

            //窗口变更的时候重设结果栏高度
            $(window).on("resize", function () {
                $("#database .well").height($(window).height() - $("#database #sqlexecute").height() - $("#ribbon").outerHeight() - $(".panel-heading").outerHeight() - 130);
            });

            //修复iOS下iframe无法滚动的BUG
            if (/iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream) {
                $("#resultparent").css({"-webkit-overflow-scrolling": "touch", "overflow": "auto"});
            }

            $(document).on("click", ".btn-compress", function () {
                Fast.api.ajax({
                    url: "general/database/backuplist",
                    type: 'get'
                }, function (data) {
                    Layer.open({
                        area: ["780px", "500px"],
                        btn: [],
                        title: "备份与还原",
                        content: Template("backuptpl", {backuplist: data.backuplist})
                    });
                    return false;
                });
                return false;
            });

            $(document).on("click", ".btn-backup", function () {
                Fast.api.ajax({
                    url: "general/database/backup",
                    data: {action: 'backup'}
                }, function (data) {
                    Layer.closeAll();
                    $(".btn-compress").trigger("click");
                });
            });

            $(document).on("click", ".btn-restore", function () {
                var that = this;
                Layer.confirm("确定恢复备份？<br><font color='red'>建议先备份当前数据后再进行恢复操作！！！</font><br><font color='red'>当前数据库将被清空覆盖，请谨慎操作！！！</font>", {
                    type: 5,
                    skin: 'layui-layer-dialog layui-layer-fast'
                }, function (index) {
                    Fast.api.ajax({
                        url: "general/database/restore",
                        data: {action: 'restore', file: $(that).data('file')}
                    }, function (data) {
                        Layer.closeAll();
                        Fast.api.ajax({
                            url: 'ajax/wipecache',
                            data: {type: 'all'},
                        }, function () {
                            Layer.alert("备份恢复成功,点击确定将刷新页面", function () {
                                top.location.reload();
                            });
                            return false;
                        });

                    });
                });
            });

            $(document).on("click", ".btn-delete", function () {
                var that = this;
                Layer.confirm("确定删除备份？", {type: 5, skin: 'layui-layer-dialog layui-layer-fast', title: '温馨提示'}, function (index) {
                    Fast.api.ajax({
                        url: "general/database/restore",
                        data: {action: 'delete', file: $(that).data('file')}
                    }, function (data) {
                        $(that).closest("tr").remove();
                        Layer.close(index);
                    });
                });
            });

            $(window).resize();
        }
    };
    return Controller;
});
