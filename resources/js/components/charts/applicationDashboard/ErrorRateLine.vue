<template>
    <apexchart ref="errorRate" type="line" height="365px" :options="chartOptions" :series="series"></apexchart>
</template>

<script>
export default {
    name:"errorRate",
    props:["chartData"],
    data(){
        return {
            series: [
                {name: "Error Rate",data: []},
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
                    shared: false,
                    x: {
                        format: 'dd/MM/yyyy HH:mm'
                    },
                    y: { 
                        formatter: function(value) { 
                            return `${value}%`
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
                colors: ['#ff0000'],
                stroke: {
                    curve: 'straight',
                    width: '2'
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
            if(Object.keys(this.chartData).length && this.chartData.error_rates){
                this.series[0].data = Object.entries(this.chartData.error_rates).map((row) => {
                    return [row[0], row[1]]
                })
            }
        }
    }
}
</script>

