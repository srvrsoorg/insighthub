<template>
    <apexchart ref="serverLoad" type="line" height="365px" :options="chartOptions" :series="series"></apexchart>
</template>

<script>
export default {
    name:"server-load",
    props:["chartData"],
    data(){
        return {
            series: [
                {name: "5 Minute",data: []},
                {name: "15 Minute",data: []},
                {name: "Cores",data: []}
            ],
            chartOptions: {
                chart: {
                    height: 350,
                    zoom: {
                        enabled: true
                    },
                    toolbar: {
                        show: true,
                        tools:{
                            download:false,
                        }
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    x: {
                        format: 'dd/MM/yyyy HH:mm'
                    },
                    y: { 
                        formatter: function(value, { series, seriesIndex, dataPointIndex, w }) { 
                            if (value === 0 || value === 0.0) {
                                return '<p>0</p>'
                             }else {
                                return value
                            }
                        } 
                    }
                },
                noData: {
                    text: 'No Data',
                    align: 'center',
                    verticalAlign: 'middle',
                    offsetX: 0,
                    offsetY: 0,
                    style: {
                        color: '#000',
                        fontSize: '16px',
                    }
                },
                dataLabels: {
                    enabled: false,
                },
                colors: ['#008FFB', '#FF0000','#fbb031','#6DE597'],
                stroke: {
                    curve: 'straight',
                    width: '2'
                },
                yaxis:{
                    min: 0,
                    tickAmount: 1,
                    labels: {
                        formatter: function (val) {
                            return parseFloat(val)
                        }
                    }
                },
                xaxis: {
                    type: 'datetime',
                    labels: {
                        format:"dd/MM/yyyy",
                        datetimeUTC: false
                    }
                }
            }
        }
    },
    watch: {
        chartData(newVal){
            this.updateChartData()
        }
    },
    created(){
        this.updateChartData()
    },
    methods:{
        updateChartData(){
            this.series[0].data = this.chartData.map((row) => {
                return [row.datetime, row.five_min_load]
            })
            this.series[1].data = this.chartData.map((row) => {
                return [row.datetime, row.fifteen_min_load]
            })
            this.series[2].data = this.chartData.map((row) => {
                return [row.datetime, row.cores]
            })
        }
    }
}
</script>

