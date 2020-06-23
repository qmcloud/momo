//认证===============开始============
    $(".aply").click(function(){
        //||reg_identity.test($("#cer_no").val())==false
        
        var reg_realName=/^(?=.*\d.*\b)/;
        var reg_phone=/^(\d{5}|\d{6}|\d{7}|\d{8}|\d{9}|\d{10}|\d{11}|\d{12}|\d{13}|\d{14}|\d{15}|\d{16}|\d{17}|\d{18}|\d{19}|\d{20}|\d{21})$/;

        var reg_identity=/^(^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$)|(^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])((\d{4})|\d{3}[Xx])$)$/;

        if($("#real_name").val()==""||reg_realName.test($("#real_name").val())==true){
           alert("请正确填写真实姓名");
        }else if(reg_phone.test($("#mobile").val())==false){
            alert("请正确输入手机号码");
        }else if($("#cer_no").val()=="" ||reg_identity.test($("#cer_no").val())==false){
            alert("请正确填写身份证号");
        }else if($(".sf1").val()==""||$(".sf2").val()==""||$(".sf3").val()==""){
            alert("请上传证件相关照片");
        }else{
            aiax();
        }
    })
    function aiax(){
        $.ajax({
            url:"./index.php?g=Home&m=Personal&a=authsave",
            dataType:"json",
            data:{
                real_name:$("#real_name").val(),
                mobile:$("#mobile").val(),
                cer_no:$("#cer_no").val(),
                front_view:$(".sf1").val(),
                back_view:$(".sf2").val(),
                handset_view:$(".sf3").val()
            },
            type:"POST",
            success:function(data){
                //console.log(data);
                if(data.ret==200){
                    //if(data.data.from_source==1){
                       // window.location.href="inke://startlive";
                    //}else{
                        window.location.href="./index.php?m=Personal&a=card";
                    //}
                }else{
                    alert(data.msg);
                }
            },
            error:function(e){
               alert(e.msg);
            }
        })
    }
    //身份证上传
    $(".shad,.shad2,.shad3").css({"height":$(".img-sfz").height()*71/92+"px"});
    function file_click(e){
        var n= e.attr("data-index");
        upload(n);
    }
    function upload(index) {
        $('#upload').empty();
        var input = '<input type="file" id="ipt-file1" name="image" /><input type="file" id="ipt-file2" name="image" /><input type="file" id="ipt-file3" name="image" />';
        $('#upload').html(input);
        var iptt=document.getElementById(index);
        if(window.addEventListener) { // Mozilla, Netscape, Firefox
            iptt.addEventListener('change',function(){
                ajaxFileUpload(index);
                var arr_img=new Array("public/rz/images/identity_face.jpg","public/rz/images/identity_back.jpg","public/rz/images/identity_handle.jpg");
                var sub=index.substr(8,1);
                $(".img-sfz[data-index="+index+"]").attr("src",arr_img[sub-1]);
                $(".shadd[data-select="+index+"]").show();
            },false);
        }else{
            iptt.attachEvent('onchange',function(){
                ajaxFileUpload(index);
                var arr_img=new Array("public/rz/images/identity_face.jpg","public/rz/images/identity_back.jpg","public/rz/images/identity_handle.jpg");
                var sub=index.substr(8,1);
                $(".img-sfz[data-index="+index+"]").attr("src",arr_img[sub-1]);
                $(".shadd[data-select="+index+"]").show();
            });
        }
        $('#'+index).click();
    }
    function ajaxFileUpload(img) {
        $("."+img).css({"width":"0px"});
        $(".box-upload[data-index="+img+"]").hide();
        $("."+img).animate({"width":"100%"},700,function(){
            var id= img;
            var num=img.substr(8,1);
            $.ajaxFileUpload
            (
                {
                    url: './index.php?g=Home&m=Personal&a=upload',
                    secureuri: false,
                    fileElementId: id,
                    data: {},
                    dataType: 'html',
                    success: function(data) {
                        data=data.replace(/<[^>]+>/g,"");
                         var str=JSON.parse(data); 
                        if(str.ret==200){
                            var sub=img.substr(8,1);
                            $(".sf"+sub).attr("value",str.data.url);
                            alert("上传成功");
                            $(".shadd[data-select="+img+"]").hide();
                            $(".box-upload[data-index="+img+"]").show();
                            $(".box-upload[data-index="+img+"] img").attr("src","public/rz/images/ok2.jpg");
                        }else{
                            alert(str.msg);
                            $(".shadd[data-select="+img+"]").hide();
                            $(".box-upload[data-index="+img+"]").show();
                            $(".box-upload[data-index="+img+"] img").attr("src","public/rz/images/no2.jpg");
                        }
                    },
                    error: function(data) {
                        alert("上传失败");
                        $(".shadd[data-select="+img+"]").hide();
                        $(".box-upload[data-index="+img+"]").show();
                        $(".box-upload[data-index="+img+"] img").attr("src","public/rz/images/no2.jpg");
                    }
                }
            )
            return true;
        });
    }