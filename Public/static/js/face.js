var face = {
	facePath:[
	    {faceName:"微笑",facePath:"0_微笑.gif"},
		{faceName:"撇嘴",facePath:"1_撇嘴.gif"},
		{faceName:"色",facePath:"2_色.gif"},
		{faceName:"发呆",facePath:"3_发呆.gif"},
		{faceName:"得意",facePath:"4_得意.gif"},
		{faceName:"流泪",facePath:"5_流泪.gif"},
		{faceName:"害羞",facePath:"6_害羞.gif"},
		{faceName:"闭嘴",facePath:"7_闭嘴.gif"},
		{faceName:"大哭",facePath:"9_大哭.gif"},
		{faceName:"尴尬",facePath:"10_尴尬.gif"},
		{faceName:"发怒",facePath:"11_发怒.gif"},
		{faceName:"调皮",facePath:"12_调皮.gif"},
		{faceName:"龇牙",facePath:"13_龇牙.gif"},
		{faceName:"惊讶",facePath:"14_惊讶.gif"},
		{faceName:"难过",facePath:"15_难过.gif"},
		{faceName:"酷",facePath:"16_酷.gif"},
		{faceName:"冷汗",facePath:"17_冷汗.gif"},
		{faceName:"抓狂",facePath:"18_抓狂.gif"},
		{faceName:"吐",facePath:"19_吐.gif"},
		{faceName:"偷笑",facePath:"20_偷笑.gif"},
	    {faceName:"可爱",facePath:"21_可爱.gif"},
		{faceName:"白眼",facePath:"22_白眼.gif"},
		{faceName:"傲慢",facePath:"23_傲慢.gif"},
		{faceName:"饥饿",facePath:"24_饥饿.gif"},
		{faceName:"困",facePath:"25_困.gif"},
		{faceName:"惊恐",facePath:"26_惊恐.gif"},
		{faceName:"流汗",facePath:"27_流汗.gif"},
		{faceName:"憨笑",facePath:"28_憨笑.gif"},
		{faceName:"大兵",facePath:"29_大兵.gif"},
		{faceName:"奋斗",facePath:"30_奋斗.gif"},
		{faceName:"咒骂",facePath:"31_咒骂.gif"},
		{faceName:"疑问",facePath:"32_疑问.gif"},
		{faceName:"嘘",facePath:"33_嘘.gif"},
		{faceName:"晕",facePath:"34_晕.gif"},
		{faceName:"折磨",facePath:"35_折磨.gif"},
		{faceName:"衰",facePath:"36_衰.gif"},
		{faceName:"骷髅",facePath:"37_骷髅.gif"},
		{faceName:"敲打",facePath:"38_敲打.gif"},
		{faceName:"再见",facePath:"39_再见.gif"},
		{faceName:"擦汗",facePath:"40_擦汗.gif"},
		
		{faceName:"抠鼻",facePath:"41_抠鼻.gif"},
		{faceName:"鼓掌",facePath:"42_鼓掌.gif"},
		{faceName:"糗大了",facePath:"43_糗大了.gif"},
		{faceName:"坏笑",facePath:"44_坏笑.gif"},
		{faceName:"左哼哼",facePath:"45_左哼哼.gif"},
		{faceName:"右哼哼",facePath:"46_右哼哼.gif"},
		{faceName:"哈欠",facePath:"47_哈欠.gif"},
		{faceName:"鄙视",facePath:"48_鄙视.gif"},
		{faceName:"委屈",facePath:"49_委屈.gif"},
		{faceName:"快哭了",facePath:"50_快哭了.gif"},
		{faceName:"阴险",facePath:"51_阴险.gif"},
		{faceName:"亲亲",facePath:"52_亲亲.gif"},
		{faceName:"吓",facePath:"53_吓.gif"},
		{faceName:"可怜",facePath:"54_可怜.gif"},
		{faceName:"菜刀",facePath:"55_菜刀.gif"},
		{faceName:"西瓜",facePath:"56_西瓜.gif"},
		{faceName:"啤酒",facePath:"57_啤酒.gif"},
		{faceName:"篮球",facePath:"58_篮球.gif"},
		{faceName:"乒乓",facePath:"59_乒乓.gif"},
		{faceName:"拥抱",facePath:"78_拥抱.gif"},
		{faceName:"握手",facePath:"81_握手.gif"}
	],
	init : function (){
		var isShowImg=false;
		$(".emotion_btn").click(function(){
			if(isShowImg==false){
				isShowImg=true;
			    if($(".faceDiv").children().length==0){
					for(var i=0;i<face.facePath.length;i++){
						$(".faceDiv").append("<img title=\""+face.facePath[i].faceName+"\" src=\"./static/images/face/"+face.facePath[i].facePath+"\" />");
					}
					$(".faceDiv>img").click(function(){
						isShowImg=false;
						face.insertAtCursor($("#chattext")[0],"["+$(this).attr("title")+"]");
					});
				}
				$(".faceDiv").css("display","block");
			}else{
				isShowImg=false;
				$(".faceDiv").css("display","none");
			}
		});
	},
	insertAtCursor:function(myField, myValue) {
		if (document.selection) {
			myField.focus();
			sel = document.selection.createRange();
			sel.text = myValue;
			sel.select();
		} else if (myField.selectionStart || myField.selectionStart == "0") {
			var startPos = myField.selectionStart;
			var endPos = myField.selectionEnd;
			var restoreTop = myField.scrollTop;
			myField.value = myField.value.substring(0, startPos) + myValue + myField.value.substring(endPos, myField.value.length);
			if (restoreTop > 0) {
				myField.scrollTop = restoreTop;
			}
			myField.focus();
			myField.selectionStart = startPos + myValue.length;
			myField.selectionEnd = startPos + myValue.length;
		} else {
			myField.value += myValue;
			myField.focus();
		}
		$(".faceDiv").css("display","none");
	}
}