export default {
    methods: {
        progressBarColor(percentageValue) {
            percentageValue = parseFloat(percentageValue);
            if (percentageValue >= 80) {
                return "#F6D2D3";
            } else if (percentageValue <= 80 && percentageValue > 60) {
                return "#fae1b0";
            } else {
               return '#B5D3FF'
            }
        },
        remainingProgressBarColor(percentageValue) {
            percentageValue = parseFloat(percentageValue);
            if (percentageValue >= 80) {
                return "#D41D24";
            } else if (percentageValue <= 80 && percentageValue > 60) {
                return "#F5A60A";
            } else {
               return '#2D5BFF'
            }
        },
    },
};
