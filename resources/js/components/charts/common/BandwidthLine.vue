<template>
    <apexchart ref="bandwidth" type="line" height="365px" :options="chartOptions" :series="series"></apexchart>
</template>

<script>
export default {
    name:"bandwidth",
    props:["chartData"],
    data(){
        return {
            series: [
                {name: "",data: []},
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
                colors: ['#673D6F', '#6DE597', '#fbb031', '#F98248', '#FF0000'],
                stroke: {
                    curve: 'straight',
                    width: '2'
                },
                yaxis:{
                    min: 0,
                    tickAmount: 1,
                    labels: {
                        formatter: function (val) {
                            return `${parseFloat(val).toFixed(3)} MB`
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
                return [row.date, row.total_bandwidth_MB]
            })
        }
    }
}
</script>

