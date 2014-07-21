<script src="http://code.highcharts.com/highcharts.js"></script>
<script src="http://code.highcharts.com/modules/exporting.js"></script>

<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>


<script>

    $(function () {
        $('#container').highcharts({
            title: {
                text: 'Respostas nos últimos '+CMS.chart.days+' dias',
                x: -20 //center
            },
//            subtitle: {
//                text: 'Source: WorldClimate.com',
//                x: -20
//            },
            xAxis: {
                categories: CMS.chart.categories
            },
            yAxis: {
                title: {
                    text: 'Quantidade de respostas'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            tooltip: {
                enabled: false,
                formatter: function() {
                    return '<b>'+ this.series.name +'</b><br/>'+
                        this.x +': '+ this.y +'°C';
                }
            },
            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true,
                        style: {
                            textShadow: '0 0 3px white, 0 0 3px white'
                        }
                    },
                    enableMouseTracking: true
                }
            },
            series: [{
                name: 'Completos',
                data: CMS.chart.series.complete
            },{
                name: 'Incompletos',
                data: CMS.chart.series.uncomplete
            }]
        });
    });


</script>