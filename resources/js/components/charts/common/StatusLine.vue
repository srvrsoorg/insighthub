<template>
    <apexchart ref="status" type="line" height="365px" :options="chartOptions" :series="series"></apexchart>
</template>

<script>
export default {
    name:"status",
    props:["chartData"],
    data(){
        return {
            series: [
                {name: "1xx Informational",data: []},
                {name: "2xx Successful",data: []},
                {name: "3xx Redirection",data: []},
                {name: "4xx Client Error", data: []},
                {name: "5xx Server Error", data: []}
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
                    x: {
                        format: 'dd/MM/yyyy'
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
           this.updateData()
        }
    },
    created(){
        this.updateData()
    },
    methods:{
        updateData(){
            this.series[0].data = this.chartData.map((row) => {
                return [row.datetime, row.status_counts.Informational]
            })
            this.series[1].data = this.chartData.map((row) => {
                return [row.datetime, row.status_counts.Successful]
            })
            this.series[2].data = this.chartData.map((row) => {
                return [row.datetime, row.status_counts.Redirection]
            })
            this.series[3].data = this.chartData.map((row) => {
                return [row.datetime, row.status_counts.Client_error]
            })
            this.series[4].data = this.chartData.map((row) => {
                return [row.datetime, row.status_counts.Server_error]
            })
        }
    }
}
</script>

