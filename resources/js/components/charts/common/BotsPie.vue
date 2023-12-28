<template>
    <apexchart ref="bots" :options="chartOptions" :series="series" height="340"></apexchart>
</template>

<script>
export default {
    name:"bots",
    props:["chartData"],
    data(){
        return {
            series: [],
            chartOptions: {
                chart: {  
                    type: 'pie'
                },
                labels: [],
                legend: {
                    show: true,
                    position: 'right',
                    formatter: (value) => {
                        return value.length > 15 ? value.substr(0,15) + '...' : value
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
                colors: ['#008FFB', '#00E396', '#FEB019', '#FF4560', '#775DD0',
                    '#3F51B5', '#4CAF50', '#FFC107', '#FF9800',
                    '#8BC34A', '#536DFE', '#00D8C9', '#FC6180', '#4E56E1',
                    '#8D6E63', '#6B0E54', '#58D8D6', '#2196F3', '#F9844A', '#47B39C'
                ],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                        }
                    }
                },
                responsive: [
                    {
                        breakpoint: 425,
                        options: {
                            chart:{
                                height: 250
                            },
                            legend:{
                                show: false,
                                position: 'bottom'
                            }
                        }
                    }
                ]
            }
        }
    },
    watch: {
        'chartData'(){
            this.updateChart()   
        }
    },
    created(){        
        this.updateChart()
    },
    methods: {
        updateChart(){
            let labels = this.chartData.map((data) => {
                if(data.bot_name == '0'){
                    return 'Other'
                }else{
                    return data.bot_name
                }
            }) 
            
            if(labels.length > 0){
                this.chartOptions = {...this.chartOptions, labels}   
                this.chartOptions.legend.show = true;  
            }else{
                this.chartOptions.legend.show = false;
            }

            this.series = this.chartData.map((data) => {
                return Number(data.total_count)
            })
        }
    }
}
</script>