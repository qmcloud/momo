function nTabs(thisObj, Num) {
	if (thisObj.className == "active") return;
	var tabList = document.getElementById("myTab").getElementsByTagName("li");
	for (i = 0; i < tabList.length; i++) {//点击之后，其他tab变成灰色，内容隐藏，只有点击的tab和内容有属性
		if (i == Num) {
			thisObj.className = "active";
			document.getElementById("myTab_Content" + i).style.display = "block";
		} else {
			tabList[i].className = "normal";
			document.getElementById("myTab_Content" + i).style.display = "none";
		}
	}
}
