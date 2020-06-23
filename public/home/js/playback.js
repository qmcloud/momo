var ybplay={
	closePorp:function()
	{
		$('#buyvip').hide();
        $('#buyvip').html("");
        document.getElementById('ds-dialog-bg').style.display='none';
	},
	player:function(id)
	{
		//这里是请求的阿里的接口 走的是api 如果需要更换 请自行更换请求地址
		$.ajax({
          cache: true,
          type: "GET",
          url:'./api/public/?service=User.getAliCdnRecord&id='+id,
          data:"",// 你的formid
          async: false,
          error: function(request)
          {
                    layer.msg("数据请求失败");
          },
          success: function(data)
          {
                    if(data.data.code!=0)
                    {
                        layer.msg(data.data.msg);
                        return !1;
                    }
                    
                    $(".event").removeClass("selected");
                    var url=data.data.info;
                    url=url['0']['url'];
                    ybplay.video(url);
                    $("#play_"+id).addClass("selected");

          }
        });
	},
	video:function(url)
	{
        var videoObject = {
            container: '#play_reft', //容器的ID或className
            variable: 'player',//播放函数名称
            //poster:_DATA.live.pull,//封面图片
            //flashplayer:true,
            video: url,		
            autoplay:false,
            flashplayer:false,
        };
        var player = new ckplayer(videoObject);
	}
}
