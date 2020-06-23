(function(){

    /* 基础 */
    // 基于准备好的dom，初始化echarts实例
    var echarts_basic = echarts.init(document.getElementById('echarts_basic'));
    // 指定图表的配置项和数据
    var echarts_basic_option = {
        tooltip: {
            trigger: 'axis'
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: {
            type: 'category',
            boundaryGap: false,
            data: data_basic.name
        },
        yAxis: {
            type: 'value'
        },
        series: [
            {
                type:'line',
                symbolSize: 8,
                itemStyle:{
                    color:'#00b7ee',
                },
                data:data_basic.data
            }
        ]
    };
    // 使用刚指定的配置项和数据显示图表。
    echarts_basic.setOption(echarts_basic_option);    
    /* 设备终端 */
    // 基于准备好的dom，初始化echarts实例
    var echarts_source = echarts.init(document.getElementById('echarts_source'));
    // 指定图表的配置项和数据
    var echarts_source_option = {
        title: {
            text: '已注册用户使用设备类型占比',
            left:'center',
            textStyle:{
                color:'#969696'
            }
        },
        tooltip : {
            trigger: 'auto',
            axisPointer : {
                type : 'shadow'
            }
        },
        xAxis : [
            {
                type : 'category',
                data : data_source.name,
                nameTextStyle:{
                    color: '#323232',
                    fontSize:30
                },
                axisTick: {
                    alignWithLabel: true
                }
            }
        ],
        yAxis : [
            {
                max:'100',
                type : 'value',
                axisLabel: {
                    show: true,
                    interval: 'auto',
                    color:'#323232',
                    formatter: '{value}%'
                }
            }
        ],
        series : [
            {
                type:'bar',
                barWidth: '60%',
                data:data_source.nums_per,
                color: function (params){
                    var colorList = data_source.color;
                    return colorList[params.dataIndex];
                },
                label: {
                    show: true, //开启显示
                    position: 'top', //在上方显示
                    formatter: '{c}%',
                    textStyle: { //数值样式
                        color: '#323232',
                        fontSize: 16
                    }
                }
            }
        ]
    };
    // 使用刚指定的配置项和数据显示图表。
    echarts_source.setOption(echarts_source_option);
    
    /* 注册渠道 */
    // 基于准备好的dom，初始化echarts实例
    var echarts_reg = echarts.init(document.getElementById('echarts_reg'));
    // 指定图表的配置项和数据
    var echarts_reg_option = {
        title: {
            text: '总注册量：'+users_total,
            left:'10%',
            bottom:'0',
            textStyle:{
                color:'#969696'
            }
        },
        tooltip : {  
            trigger: 'item',  
            formatter: "{c}"  
        },
        legend: {  
            orient : 'vertical',  
            left : '70%', 
            top:40,
            itemWidth:10,
            itemHeight:10,
            formatter: '{name}',
            textStyle:{
                color: '#000000',
                fontSize:16
            },
            data:data_type.name
        }
        ,   
        calculable : true,  
        series : [
            {  
                type:'pie',  
                radius : '70%',//饼图的半径大小  
                center: ['35%', '40%'],//饼图的位置 
                label:{            //饼图图形上的文本标签
                    show:true,
                    position:'inner', //标签的位置
                    textStyle : {
                        fontWeight : 300 ,
                        fontSize : 16    //文字的字体大小
                    },
                    formatter:'{d}%'
                },
                data:data_type.v_n
            }
        ]
    };
    // 使用刚指定的配置项和数据显示图表。
    echarts_reg.setOption(echarts_reg_option);
    
    /* 财务 */
    // 基于准备好的dom，初始化echarts实例
    var echarts_charge = echarts.init(document.getElementById('echarts_charge'));
    // 指定图表的配置项和数据
    var echarts_charge_option = {
        tooltip : {
            trigger: 'auto',
            axisPointer : {
                type : 'shadow'
            }
        },
        xAxis : [
            {
                type : 'category',
                data : data_charge.name,
                nameTextStyle:{
                    color: '#323232',
                    fontSize:30
                },
                axisTick: {
                    alignWithLabel: true
                }
            }
        ],
        yAxis : [
            {
                name:'(元)',
                type : 'value',
                axisLabel: {
                    show: true,
                    interval: 'auto',
                    color:'#323232',
                    formatter: '{value}'
                },
                axisLine:{
                    show:false,        
                }
            }
        ],
        series : [
            {
                type:'bar',
                barWidth: '60%',
                data:data_charge.money,
                color: function (params){
                    var colorList = data_charge.color;
                    return colorList[params.dataIndex];
                },
                label: {
                    show: true, //开启显示
                    position: 'top', //在上方显示
                    formatter: function(a) {
                        var result = [],
                            counter = 0,
                            num = a.data;
                        num = num.toString().replace(/\$|\,/g,'');
                        if(isNaN(num)){
                            num = "0";
                        }
                            
                        sign = (num == (num = Math.abs(num)));
                        num = Math.floor(num*100+0.50000000001);
                        cents = num%100;
                        num = Math.floor(num/100).toString();
                        if(cents<10)
                        cents = "0" + cents;
                        for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
                        num = num.substring(0,num.length-(4*i+3))+','+
                        num.substring(num.length-(4*i+3));
                        return (((sign)?'':'-') + num + '.' + cents);
                    },
                    textStyle: { //数值样式
                        color: '#323232',
                        fontSize: 16
                    }
                }

            }
        ]
    };
    // 使用刚指定的配置项和数据显示图表。
    echarts_charge.setOption(echarts_charge_option);

    /* ajax */
    function getData(request_data){
        $.ajax({
            url:'/index.php?g=admin&m=Main&a=getdata',
            type:'POST',
            data:request_data,
            dataType:'json',
            success:function(data){
                var code=data.code;
                var info=data.info;
                var msg=data.msg;
                if(code!=0){
                    alert(msg);
                    return !1;
                }
                var action=request_data.action;
                switch(action){
                    case '1':
                        /* 基本指标 */
                        $(".basic_list li[data-type='"+request_data.basic_type+"'] .basic_list_n span").text(info.nums);
                        if(request_data.basic_type==3){
                            echarts_basic_option.yAxis.name='分钟';
                        }else{
                            echarts_basic_option.yAxis.name='';
                        }
                        echarts_basic_option.xAxis.data=info.name,
                        echarts_basic_option.series[0].data=info.data,
                        echarts_basic.setOption(echarts_basic_option);
                        break;
                    case '2':
                        /* 用户画像 */
                        break;
                    case '3':
                        /* 主播数据 */
                        $("#anchor_live_today").text(info.anchor_live_today);
                        $("#anchor_live_long_today").text(info.anchor_live_long_today);
                        break;
                    case '4':
                        /* 财务 */
                        echarts_charge_option.series[0].data=info.money,
                        echarts_charge.setOption(echarts_charge_option);
                        break;
                    case '5':
                        /* 提现 */
                        $("#cash_apply").text(info.cash_apply);
                        $("#cash_adopt").text(info.cash_adopt);
                        $("#cash_anchor").text(info.cash_anchor);
                        break;
                }
            },
            error:function(){
                
            }
        })
    }
    /* 天数选择 */
    $(".dropdown_input").click(function(){
        var _this=$(this);
        _this.siblings(".dropdown_list").toggle();
    })
    
    $(".dropdown_list li").click(function(){
        var _this=$(this);
        var type=_this.data('type');
        var li_text=_this.text();
        
        _this.parents('.dropdown').find(".dropdown_input").text(li_text);
        var action=_this.parents('.bd_title').find(".action").val();
        _this.parents(".dropdown_list").toggle();
        _this.parents('.bd_title').find(".dropdown_input").data('type',type);
        _this.parents('.bd_title').find("input[name=start_time]").val('');
        _this.parents('.bd_title').find("input[name=end_time]").val('');
        var basic_type=0;
        if(action==1){
            var basic_type=_this.parents('.basic').find(".basic_list li.on").data('type');
        }
        
        var start_time=0;
        var end_time=0;
        var request_data={action:action,type:type,start_time:start_time,end_time:end_time,basic_type:basic_type};
        getData(request_data);
    })
    
    
    $(".search").click(function(){
        var _this=$(this);
        var start_time=_this.parents('.bd_title').find("input[name=start_time]").val();
        var end_time=_this.parents('.bd_title').find("input[name=end_time]").val();
        if(!start_time){
            alert('请选择时间');
            return !1;
        }
        
        if(!end_time){
            alert('请选择时间');
            return !1;
        }
        
        var type=0;
        _this.parents('.bd_title').find(".dropdown_input").data('type',type);
         
        var action=_this.parents('.bd_title').find(".action").val();

        var basic_type=0;
        if(action==1){
            var basic_type=_this.parents('.basic').find(".basic_list li.on").data('type');
        }

        var request_data={action:action,type:type,start_time:start_time,end_time:end_time,basic_type:basic_type};
        getData(request_data);
    })
    
    $(".basic_list li.active").click(function(){
        var _this=$(this);
        
        _this.siblings().removeClass("on");
        _this.addClass("on");
        var basic_type=$(this).data('type');
        var type=_this.parents('.basic').find(".dropdown_input").data('type');

        var action=_this.parents('.basic').find(".action").val();
        var start_time=_this.parents('.basic').find("input[name=start_time]").val();
        var end_time=_this.parents('.basic').find("input[name=end_time]").val();

        var request_data={action:action,type:type,start_time:start_time,end_time:end_time,basic_type:basic_type};
        getData(request_data);
    })
    
    
    $(".export").click(function(){
        var _this=$(this);
        
        var type=_this.parents('.bd_title').find(".dropdown_input").data('type');
        var action=_this.parents('.bd_title').find(".action").val();
        var start_time=_this.parents('.bd_title').find("input[name=start_time]").val();
        var end_time=_this.parents('.bd_title').find("input[name=end_time]").val();
        
        var basic_type=0;
        if(action==1){
            var basic_type=_this.parents('.basic').find(".basic_list li.on").data('type');
        }

        location.href='/index.php?g=admin&m=Main&a=export&action='+action+'&type='+type+'&start_time='+start_time+'&end_time='+end_time+'&basic_type='+basic_type;
    })
    
})()