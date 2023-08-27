define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'template', 'echarts', 'echarts-theme'], function ($, undefined, Backend, Table, Form, Template, Echarts) {

    var Controller = {
        index: function () {
            //这句话在多选项卡统计表时必须存在，否则会导致影响的图表宽度不正确
            $(document).on("click", ".charts-custom a[data-toggle=\"tab\"]", function () {
                var that = this;
                setTimeout(function () {
                    var id = $(that).attr("href");
                    var chart = Echarts.getInstanceByDom($(id)[0]);
                    chart.resize();
                }, 0);
            });

            // 基于准备好的dom，初始化echarts实例
            var lineChart = Echarts.init(document.getElementById('line-chart'), 'walden');

            // 指定图表的配置项和数据
            var option = {
                xAxis: {
                    type: 'category',
                    data: ['周一', '周二', '周三', '周四', '周五', '周六', '周日']
                },
                yAxis: {
                    type: 'value'
                },
                series: [{
                    data: [49, 92, 61, 134, 90, 130, 120],
                    type: 'line'
                }]
            };

            // 使用刚指定的配置项和数据显示图表。
            lineChart.setOption(option);
            // 基于准备好的dom，初始化echarts实例
            var areaChart = Echarts.init(document.getElementById('area-chart'), 'walden');

            // 指定图表的配置项和数据
            var option = {
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: ['周一', '周二', '周三', '周四', '周五', '周六', '周日']
                },
                yAxis: {
                    type: 'value'
                },
                series: [{
                    data: [820, 932, 901, 934, 1290, 1330, 1320],
                    type: 'line',
                    areaStyle: {}
                }]
            };

            // 使用刚指定的配置项和数据显示图表。
            areaChart.setOption(option);

            var pieChart = Echarts.init(document.getElementById('pie-chart'), 'walden');
            var option = {
                tooltip: {
                    trigger: 'item',
                    formatter: '{a} <br/>{b}: {c} ({d}%)'
                },
                legend: {
                    orient: 'vertical',
                    left: 10,
                    data: ['直接访问', '邮件营销', '联盟广告', '视频广告', '搜索引擎']
                },
                series: [
                    {
                        name: '访问来源',
                        type: 'pie',
                        radius: ['50%', '70%'],
                        avoidLabelOverlap: false,
                        label: {
                            normal: {
                                show: false,
                                position: 'center'
                            },
                            emphasis: {
                                show: true,
                                textStyle: {
                                    fontSize: '30',
                                    fontWeight: 'bold'
                                }
                            }
                        },
                        labelLine: {
                            normal: {
                                show: false
                            }
                        },
                        data: [
                            {value: 335, name: '直接访问'},
                            {value: 310, name: '邮件营销'},
                            {value: 234, name: '联盟广告'},
                            {value: 135, name: '视频广告'},
                            {value: 1548, name: '搜索引擎'}
                        ]
                    }
                ]
            };
            // 使用刚指定的配置项和数据显示图表。
            pieChart.setOption(option);

            var barChart = Echarts.init(document.getElementById('bar-chart'), 'walden');
            option = {
                legend: {},
                tooltip: {},
                dataset: {
                    source: [
                        ['产品销售', '2015', '2016', '2017'],
                        ['风扇', 43.3, 85.8, 93.7],
                        ['电视机', 83.1, 73.4, 55.1],
                        ['空调', 86.4, 65.2, 82.5],
                        ['冰箱', 72.4, 53.9, 39.1]
                    ]
                },
                xAxis: {type: 'category'},
                yAxis: {},
                // Declare several bar series, each will be mapped
                // to a column of dataset.source by default.
                series: [
                    {type: 'bar'},
                    {type: 'bar'},
                    {type: 'bar'}
                ]
            };
            // 使用刚指定的配置项和数据显示图表。
            barChart.setOption(option);


            var barChart = Echarts.init(document.getElementById('simplebar-chart'));
            option = {
                xAxis: {
                    type: 'category',
                    axisLine: {
                        lineStyle: {
                            color: "#fff"
                        }
                    },
                    data: ['周一', '周二', '周三', '周四', '周五', '周六', '周日']
                },
                yAxis: {
                    type: 'value',
                    axisLine: {
                        lineStyle: {
                            color: "#fff"
                        }
                    }
                },
                series: [{
                    data: [120, 200, 150, 80, 70, 110, 130],
                    type: 'bar',
                    itemStyle: {
                        color: "#fff",
                        opacity: 0.6
                    }
                }]
            };
            // 使用刚指定的配置项和数据显示图表。
            barChart.setOption(option);

            var barChart = Echarts.init(document.getElementById('smoothline-chart'));
            option = {
                textStyle: {
                    color: "#fff"
                },
                color: ['#fff'],
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: ['周一', '周二', '周三', '周四', '周五', '周六', '周日'],
                    axisLine: {
                        lineStyle: {
                            color: "#fff"
                        }
                    }
                },
                yAxis: {
                    type: 'value',
                    splitLine: {
                        show: false
                    },
                    axisLine: {
                        lineStyle: {
                            color: "#fff"
                        }
                    }
                },
                series: [{
                    data: [820, 932, 901, 934, 1290, 1330, 1320],
                    type: 'line',
                    smooth: true,
                    areaStyle: {
                        opacity: 0.4
                    }

                }]
            };
            // 使用刚指定的配置项和数据显示图表。
            barChart.setOption(option);
        }
    };
    return Controller;
});
