<template>
    <apexchart ref="accessLogs" type="line" height="365px" :options="chartOptions" :series="series"></apexchart>
</template>

<script>
export default {
    name:"accessLogs",
    props:["chartData"],
    data(){
        return {
            series: [
                {name: "Access Logs",data: []},
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
                    }              
                },
                yaxis:{
                    labels: {
                        formatter: function (val) {
                            return parseFloat(val).toFixed()
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
                colors: ['#5F79E9'],
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
            if(this.chartData.length){
                this.series[0].data = this.chartData.map((row) => {
                    return [row.date, row.count]
                })
            }
        }
    }
}
</script>

