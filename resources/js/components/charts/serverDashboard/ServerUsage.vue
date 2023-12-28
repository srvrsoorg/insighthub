<template>
    <apexchart ref="serverUsage" type="line" height="365px" :options="chartOptions" :series="series"></apexchart>
</template>

<script>
export default {
    name:"server-usage",
    props:["chartData"],
    data(){
        return {
            series: [
                {name: "CPU Usage",data: []},
                {name: "Memory Usage",data: []},
                {name: "Disk Usage",data: []},
                {name: "Swap Usage",data: []}
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
                colors: ['#2D5BFF', '#775DD0', '#FF4560', '#FEB019'],
                tooltip: {
                    x: {
                        format: 'dd/MM/yyyy HH:mm'
                    },
                    y: { 
                        formatter: function(value, { series, seriesIndex, dataPointIndex, w }) { 
                            if (value === 0 || value === 0.0) {
                                return '<p>0%</p>' // Display '0' for zero values
                             }else {
                                return `${value}%`
                            }
                        } 
                    }             
                },
                dataLabels: {
                    enabled: false,
                },
                stroke: {
                    curve: 'straight',
                    width: 2,
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
    watch:{
        chartData(newVal){
           this.setData()
        }
    },
    created(){
        this.setData()
    },
    methods: {
        setData(){
            this.series[0].data = this.chartData.map((row) => {
                return [row.datetime, row.cpu_usage]
            })
            this.series[1].data = this.chartData.map((row) => {
                return [row.datetime, row.memory_usage]
            })
            this.series[2].data = this.chartData.map((row) => {
                return [row.datetime, row.disk_usage]
            })
            this.series[3].data = this.chartData.map((row) => {
                return [row.datetime, row.swap_usage]
            })
        }
    }
}
</script>

