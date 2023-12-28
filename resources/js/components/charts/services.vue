<template>
    <apexchart ref="services" type="line" height="365px" :options="chartOptions" :series="series"></apexchart>
</template>

<script>
export default {
    name:"services",
    props:["chartData"],
    data(){
        return {
            series: [
                {name: "CPU Usage",data: []},
                {name: "Memory Usage",data: []},
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
                tooltip: {
                    x: {
                        format: 'dd/MM/yyyy HH:mm'
                    },
                    y: { 
                        formatter: function(value, { series, seriesIndex, dataPointIndex, w }) { 
                            if (value === 0 || value === 0.0) {
                                return '<p>0%</p>'// Display '0' for zero values
                             }else {
                                return `${value}%`
                            }
                        } 
                    }              
                },
                dataLabels: {
                    enabled: false,
                },
                colors: ['#fbb031','#6DE597'],
                stroke: {
                    curve: 'straight',
                    width: '2'
                },
                yaxis:{
                    min: 0,
                    tickAmount: 1,
                    labels: {
                        formatter: function (val) {
                            return `${parseFloat(val).toFixed(2)}%`
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
            this.updateData()
        }
    },
    created(){
        this.updateData()
    },
    methods:{
        updateData(){
            this.series[0].data = this.chartData.map((row) => {
                return [row.datetime, row.cpu_usage]
            })
            this.series[1].data = this.chartData.map((row) => {
                return [row.datetime, row.memory_usage]
            })
        }
    }
}
</script>

