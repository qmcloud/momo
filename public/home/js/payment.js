$(function(){
	$("#package-list .item").click(function(){
		$(this).siblings().removeClass("active");
		$(this).addClass("active");
		var changeid=$(this).attr("data-id");
		var price=$(this).attr("data-price");

		$("#changeid").val(changeid);
		//$("#price").val(price);
		$(".charge-cost .cost").html(price);
	})
	
	$("#charge-source-list .item").click(function(){
		$(this).siblings().removeClass("active");
		$(this).addClass("active");
		var source=$(this).attr("data-source");
		$("#source").val(source);
		 document.getElementById('c_PPPayID').value = source;
	})	
	
	$("#price").on("keydown", function(e) {
      if (!A(e.keyCode)) return ! 1
  }).on("keyup",function(e) {
      O($(this).val())
  }).on("blur",function(e) {
      O($(this).val(), !0)
  })
})

var O=function(e, n) {
		var s=2e5, o = 1;
		e = parseInt(e) || 0,
		e > s ? ($("#price").val(e = s), $(".hjdou-tip").show().html('<img src="https://p.ssl.qhmsg.com/t0137558d5f78e0c263.png">充值金额最大不能超过' + s / 1e4 + "万元")) : e < o && n ? ($("#price").val(e = o), $(".hjdou-tip").show().html('<img src="https://p.ssl.qhmsg.com/t0137558d5f78e0c263.png">充值金额最小不能低于' + o + "元")) : (e != $("#price").val() && $("#price").val(e), $(".hjdou-tip").hide()),
		$(".hjdou").html(e*10),
		$(".charge-cost .cost").html(e)
}


function charge_submit()
{
	 var a = jQuery("[class='item weixin active']").attr("data-index");
	 if($("#package-list .active").length>0)
	 {
		 if($("#charge-source-list .active").length>0)
		 {
			 $("#fpost").submit();
		 }
		 else
		 {
			 alert("请选择支付方式");
		 }
		  
	 }
	 else
	 {
		 alert("请选择充值金额");
	 }
	
}