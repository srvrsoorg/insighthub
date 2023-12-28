<template>
    <apexchart ref="referrer" type="line" height="300px" :options="chartOptions" :series="series"></apexchart>
</template>

<script>
export default {
    name:"referrer",
    props:["chartData"],
    data(){
        return {
            series: [
                {name: "Referrer",data: []},
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
                colors: ['#fbb031'],
                stroke: {
                    curve: 'straight',
                    width: '2'
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
                return [row.date, row.hits]
            })
        }
    }
}
</script>

