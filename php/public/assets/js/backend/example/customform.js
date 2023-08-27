define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            Template.helper("Fast", Fast);

            //因为日期选择框不会触发change事件，导致无法刷新textarea，所以加上判断
            $(document).on("dp.change", "#second-form .datetimepicker", function () {
                $(this).parent().prev().find("input").trigger("change");
            });
            $(document).on("fa.event.appendfieldlist", "#first-table .btn-append", function (e, obj) {

            });
            $(document).on("fa.event.appendfieldlist", "#second-table .btn-append", function (e, obj) {
                //绑定动态下拉组件
                Form.events.selectpage(obj);
                //绑定日期组件
                Form.events.datetimepicker(obj);
                //绑定上传组件
                Form.events.faupload(obj);

                //上传成功回调事件，变更按钮的背景
                $(".upload-image", obj).data("upload-success", function (data) {
                    $(this).css("background-image", "url('" + Fast.api.cdnurl(data.url) + "')");
                })
            });
            Form.api.bindevent($("form[role=form]"), function (data, ret) {
                Layer.alert(data.data);
            });
        },
    };
    return Controller;
});
