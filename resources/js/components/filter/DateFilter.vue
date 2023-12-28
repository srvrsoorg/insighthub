<template>
    <div class="flex sm:flex-nowrap flex-wrap gap-4 w-full max-w-2xl">
        <VueDatePicker
            ref="datepicker"
            v-model="date"
            range
            placeholder="Select Date"
            :enable-time-picker="false"
            :preset-dates="presetDateRanges"
            :clearable="false"
        >
            <template #action-buttons>
                <button
                    @click="selectDate()"
                    :class="[isLightColor ? 'bg-custom-600' : 'bg-custom-500','text-white text-sm rounded-md px-2 py-1']"
                >
                    Select
                </button>
            </template>
            <template #preset-date-range-button="{ label, value, presetDate }">
                <span
                    role="button"
                    :tabindex="0"
                    @click="presetDate(value)"
                    @keyup.enter.prevent="presetDate(value)"
                    @keyup.space.prevent="presetDate(value)"
                >
                    {{ label }}
                </span>
            </template>
        </VueDatePicker>
    </div>
</template>

<script>
import { startOfDay, endOfDay, subDays, endOfMonth, endOfYear, startOfMonth, startOfYear, subMonths } from 'date-fns';
import { mapActions, mapState } from 'pinia';
import { useFilterStore } from '@/store/filter';

export default {
    data(){
        return{
            date: []
        }
    },
    created(){
        this.date = this.dates
    },
    computed:{
        ...mapState(useFilterStore, ['dates']),
        presetDateRanges() {
            return [
                { label: 'Today', value: [startOfDay(new Date()), endOfDay(new Date())] },
                { label: 'Week', value: [subDays(new Date(), 7), new Date()] },
                { label: 'This month', value: [startOfMonth(new Date()), endOfMonth(new Date())] },
                { label: 'Last month', value: [startOfMonth(subMonths(new Date(), 1)), endOfMonth(subMonths(new Date(), 1))] },
                { label: 'This year', value: [startOfYear(new Date()), endOfYear(new Date())] },
            ];
        }
    },
    methods: {
        ...mapActions(useFilterStore, ['setDate']),
        selectDate(){
            this.$refs.datepicker.selectDate()
        },
    },
    watch: {
        date(val) {
            this.setDate(val)
        }
    }
};
</script>
