<template>
    <div id="chart">
        <apexchart type="bar" height="300" :options="chartOptions" :series="series"></apexchart>
      </div>
</template>

<script>
export default{
    props: ['chartData'],
    data(){
        return{
            series: [
                {
                    name: "Disk Usage",
                    data: [],
                }
            ],
            chartOptions: {
                chart: {
                    type: 'bar',
                    height: 300
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
                plotOptions: {
                    bar: {
                        borderRadius: 2,
                        horizontal: true,
                        barHeight: "20px",
                    }
                },
                grid:{
                    show: false
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    type: 'category',
                    categories: [],
                    tickAmount: 3,
                    labels:{
                        formatter: (val) => {
                            return `${val.toFixed(2)}%`
                        }
                    }
                },
                tooltip: {
                    shared: false,
                    intersect: false,
                    y: {
                        formatter: function (val) {
                            return `${val.toFixed(2)}%`;
                        },
                    },
                },
                responsive: [
                    {
                        breakpoint: 400,
                        options: {
                            plotOptions: {
                                bar: {
                                    horizontal: false,
                                }
                            },
                            xaxis: {
                                labels:{
                                    trim: true,
                                    formatter: (val) => {
                                        return val;
                                    }
                                }
                            },
                            yaxis:{
                                labels:{
                                    formatter: (val) => {
                                        return `${val.toFixed(2)}%`
                                    }
                                }
                            },
                        }
                    }
                ]
            },
        }
    },
    watch: {
        chartData(newVal) { 
            this.updateChart()
        }
    },
    created() {
        this.updateChart()
    },
    methods: {
        updateChart(){
            if(this.chartData.length){
                let categories = this.chartData.map(val => {
                    return val.server_name
                })

                this.chartOptions.xaxis = {
                    ...this.chartOptions.xaxis,
                    categories,
                };
        
                this.series[0].data = this.chartData.map(val => {
                    return val.disk_in_pr
                });
            }
        }
    }
}
</script>
