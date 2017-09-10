function loadRegion(sel,type_id,selName,url){
    $("#"+selName+" option").remove();
    $("<option value=0>请选择</option>").appendTo($("#"+selName));
    if($("#"+sel).val()==0) return;

    $.getJSON(url,{
        pid:$("#"+sel).val(),
        type:type_id
    },function(data){
        if(data){
            $.each(data,function(idx,item){
                $("<option value="+item.id+">"+item.name+"</option>").appendTo($("#"+selName));
            });
        }else{
            $("<option value='0'>请选择</option>").appendTo($("#"+selName));
        }
        if(type_id==2)
            $("#town").html("<option value='0'>镇/区</option>");
    } );
}