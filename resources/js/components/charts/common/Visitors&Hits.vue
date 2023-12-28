<template>
    <apexchart ref="ipAddress" type="line" height="365px" :options="chartOptions" :series="series"></apexchart>
</template>

<script>
export default {
    name:"ip-address",
    props:["chartData"],
    data(){
        return {
            series: [
                {name: "Visitors",data: []},
                {name: "Hits", data: []}
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
                colors: ['#6B0E54', '#8BC34A',],
                tooltip: {
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
                            return `${parseFloat(val)}`
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
            this.series[0].data = newVal.map((row) => {
                return [row.date, row.visitors]
            })
            this.series[1].data = newVal.map((row) => {
                return [row.date, row.hits]
            })
        }
    },
    created(){
        this.series[0].data = this.chartData.map((row) => {
            return [row.date, row.visitors]
        })
        this.series[1].data = this.chartData.map((row) => {
            return [row.date, row.hits]
        })
    }
}
</script>

