<template>
    <apexchart
        type="bar"
        height="350"
        :options="chartOptions"
        :series="series"
    ></apexchart>
</template>

<script>
export default {
    name: "Application",
    props: ["chartData"],
    data() {
        return {
            series: [
                {
                    name: "Total",
                    data: [],
                }
            ],
            chartOptions: {
                chart: {
                    type: "bar",
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
                        horizontal: true,
                        columnWidth: "20px",
                        barHeight: "20px",
                        endingShape: "rounded",
                    },
                },
                colors: ['#58D8D6', '#F9844A'],
                dataLabels: {
                    enabled: false,
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ["transparent"],
                },
                xaxis: {
                    type: 'category',
                    categories: [],
                    tickAmount: 4
                },
                fill: {
                    opacity: 1,
                },
                tooltip: {
                    shared: false,
                    intersect: false,
                    y: {
                        formatter: function (val) {
                            return val;
                        },
                    },
                },
            },
        };
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
            let categories = this.chartData.map(val => {
                return val.name
            })
            this.chartOptions.xaxis = {
                ...this.chartOptions.xaxis,
                categories,
            };

            this.series[0].data = this.chartData.map(val => {
                return val.access_logs_count
            });
        }
    }
};
</script>
