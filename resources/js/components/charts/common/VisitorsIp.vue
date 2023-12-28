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
    name: "VisitorIPs",
    props: ["hits"],
    data() {
        return {
            series: [
                {
                    name: "Hits",
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
                        horizontal: false,
                        columnWidth: "20px",
                        barHeight: "20px",
                        endingShape: "rounded",
                    },
                },
                colors: ['#17ead9'],
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
                },
                fill: {
                    type: "gradient",
                    gradient: {
                        gradientToColors: ["#6078ea"],
                        shade: "dark",
                        type: "vertical",
                        shadeIntensity: 0.7,
                        inverseColors: false,
                        opacityFrom: 1,
                        opacityTo: 1,
                        stops: [0, 100],
                    }
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
        hits(newVal) { 
            this.updateChart()
        }
    },
    created() {
        this.updateChart()
    },
    methods: {
        updateChart(){
            let categories = this.hits.map(val => {
                return val.ip
            })

            this.chartOptions = {
                ...this.chartOptions,
                xaxis:{
                    type: 'category',  
                    categories
                }
            } 

            this.series[0].data = this.hits.map(val => {
                return val.total_count
            });
        }
    }
};
</script>
