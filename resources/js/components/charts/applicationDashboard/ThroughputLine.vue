<template>
    <apexchart ref="throughput" type="line" height="365px" :options="chartOptions" :series="series"></apexchart>
</template>

<script>
export default {
    name:"throughput",
    props:["chartData"],
    data(){
        return {
            series: [
                {name: "Throughput",data: []},
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
                            return `${value}/hr`
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
                colors: ['#29ABE2'],
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
            if(Object.keys(this.chartData).length && this.chartData.request_per){
                this.series[0].data = Object.entries(this.chartData.request_per).map((row) => {
                    return [row[0], row[1]]
                })
            }
        }
    }
}
</script>

