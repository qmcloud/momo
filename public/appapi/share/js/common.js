/**
*直播间js
*编码utf8
*/

var myVideo=document.getElementById("video1");

var Ctrfn={
    play:function(objbtn){
        var myVideo=document.getElementById("videoHLS_html5_api");
        objbtn.parent().hide();
        $(".jw-preview").hide();
        //$(".down-bottom").hide();
        myVideo.play();
    }


}