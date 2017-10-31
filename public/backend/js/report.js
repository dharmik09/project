function Level1And2Report(questionText,total,chartType,chartData,key)
{
    $('#highchart_option_'+key).highcharts({
            colors: ['#f58634', '#5cc6d0', '#cc93ad'],
            chart: {
                type: chartType,
            },
            title: {
                text: questionText
            },
            subtitle: {
                text: '<strong>Total VOTES</strong>:'+total
            },
            xAxis: {
                type: 'category',
                width: '450'
            },
            legend: {
                enabled:false
            },
            yAxis: {
                min: 0,
                max: 100,
                tickInterval: 10,
                title: {
                    text: 'Teens'
                },                
                lineWidth: 1                
            },
            
            plotOptions: {
                series: {
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        format: '{point.y:.1f}%'
                    }
                }
            },
            tooltip: {
                pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b><br/>'
            },
            series: [{
                    colorByPoint: true,
                    data: chartData
                }]
           
        });
}