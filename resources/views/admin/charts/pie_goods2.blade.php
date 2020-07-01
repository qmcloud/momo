<!-- 为ECharts准备一个具备大小（宽高）的Dom -->
<div id="main2" style="width:100%;min-width: 450px;height:400px;"></div>
<script>
    $(function () {
        // 基于准备好的dom，初始化echarts实例
        var app = echarts.init(document.getElementById('main2'));
        app.title = '嵌套环形图';

        // 指定图表的配置项和数据
        option = {
            title : {
                text: '展位商品统计',
                subtext: '百分比',
                x:'center'
            },
            tooltip : {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            legend: {
                orient: 'vertical',
                left: 'left',
                data: {!! json_encode($cate) !!}
            },
            series : [
                {
                    name: '访问来源',
                    type: 'pie',
                    radius : '55%',
                    center: ['50%', '60%'],
                    data:{!! json_encode($data) !!},
                    itemStyle: {
                        emphasis: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }
            ]
        };

        // 使用刚指定的配置项和数据显示图表。
        app.setOption(option);
    });
</script>