<template>
    <apexchart
      dir="ltr"
      height="130"
      width="100"
      :series="series"
      :options="chartOptions"
    ></apexchart>
  </template>
  
  <script>
  export default {
    props:['diskUsage', 'bg', 'color'],
    data() {
        return {
            series: [parseFloat(this.diskUsage)],
            chartOptions: {
                chart: {
                  type: "radialBar",
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
                  radialBar: {
                    startAngle: -135,
                    endAngle: 225,
                    hollow: {
                      margin: 0,
                      size: '60%',
                      background: '#fff',
                      image: undefined,
                      imageOffsetX: 0,
                      imageOffsetY: 0,
                      position: 'front',
                    },
                    track: {
                      background: this.bg,
                      strokeWidth: '80%',
                      margin: 0, // margin is in pixels
                    },
                    dataLabels: {
                      show: true,
                      name: {
                        show: false
                      },
                      value: {
                        offsetY: 5,
                        fontSize: '12px',
                        color: "#000000",
                        fontFamily: 'DM Sans',
                      }
                    }
                  },
                },
                fill: {
                  type: 'solid',
                  colors: this.color
                },
            },
        };
    },
  };
  </script>
  
  
  