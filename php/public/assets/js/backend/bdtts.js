define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置

            Form.api.bindevent($("form[role=form]"), function(data,ret){

                console.log(data.filename);

                if(data.filename){
                    $('#testingaudio').attr('src',data.filename);
                }
                //return false;
            }, function(data, ret){
                //return false;
            }, function(success, error){
                //return false;
            });

            // $('.bt-geturl').click(function(){
            //     var str = $('[name=q]').val().replace(/\s+/g,"");
            //     console.log(str);

            //     if(str == ""){
            //         layer.msg('请先填写内容!');
            //         $('[name=q]').focus();
            //         return false;
            //     }

            //     // console.log($("form[role=form]").serialize());
            //     var apiurl = $(this).attr('data-url'); 
            //     Fast.api.ajax({
            //         url:apiurl,
            //         loading:false,
            //         data:$("form[role=form]").serialize(),
            //         type:'get',
            //     }, function(data, ret){
            //         //成功回调
            //         // alert(ret);
            //         // console.log(ret);
            //         //layer.msg(data.url);
            //         layer.open({
            //           type: 1,
            //           skin: 'layui-layer-demo', //样式类名
            //           area: ['80%', '150px'],
            //           closeBtn: 1, //不显示关闭按钮
            //           anim: 2,
            //           shadeClose: true, //开启遮罩关闭
            //           content: '<div style="padding:10px;">服务器请求地址：<br/>'+data.trueurl+'<br/>百度接口请求地址：<br/>'+data.url+'</div>'
            //         });

            //         // console.log(data);
            //     });


            // });

        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"),function(data,ret){
                    console.log('abc');
                });
            },

        }
    };
    return Controller;
});