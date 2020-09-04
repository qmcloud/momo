<canvas id="scatter" style="width: 100%;"></canvas>
<script>
$(function () {
    function randomScalingFactor() {
        return Math.floor(Math.random() * 100)
    }

    window.chartColors = {
        red: 'rgb(255, 99, 132)',
        orange: 'rgb(255, 159, 64)',
        yellow: 'rgb(255, 205, 86)',
        green: 'rgb(75, 192, 192)',
        blue: 'rgb(54, 162, 235)',
        purple: 'rgb(153, 102, 255)',
        grey: 'rgb(201, 203, 207)'
    };

    var ctx = document.getElementById("scatter").getContext('2d');
    var scatterChart = new Chart(ctx, {
        type: 'scatter',
        data: {
            datasets: [{
                label: 'My First dataset',
                borderColor: window.chartColors.red,
                data: [{
                    x: randomScalingFactor(),
                    y: randomScalingFactor(),
                }, {
                    x: randomScalingFactor(),
                    y: randomScalingFactor(),
                }, {
                    x: randomScalingFactor(),
                    y: randomScalingFactor(),
                }, {
                    x: randomScalingFactor(),
                    y: randomScalingFactor(),
                }, {
                    x: randomScalingFactor(),
                    y: randomScalingFactor(),
                }, {
                    x: randomScalingFactor(),
                    y: randomScalingFactor(),
                }, {
                    x: randomScalingFactor(),
                    y: randomScalingFactor(),
                }]
            }, {
                label: 'My Second dataset',
                borderColor: window.chartColors.blue,
                data: [{
                    x: randomScalingFactor(),
                    y: randomScalingFactor(),
                }, {
                    x: randomScalingFactor(),
                    y: randomScalingFactor(),
                }, {
                    x: randomScalingFactor(),
                    y: randomScalingFactor(),
                }, {
                    x: randomScalingFactor(),
                    y: randomScalingFactor(),
                }, {
                    x: randomScalingFactor(),
                    y: randomScalingFactor(),
                }, {
                    x: randomScalingFactor(),
                    y: randomScalingFactor(),
                }, {
                    x: randomScalingFactor(),
                    y: randomScalingFactor(),
                }]
            }]
        },
        options: {
            scales: {
                xAxes: [{
                    type: 'linear',
                    position: 'bottom'
                }]
            }
        }
    });
});
</script>