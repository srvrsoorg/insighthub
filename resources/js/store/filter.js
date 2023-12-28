import { defineStore } from 'pinia'
import { subDays } from 'date-fns';

export const useFilterStore = defineStore({
    id: 'filter',
    state: () => ({
        dates: [subDays(new Date(), 7), new Date()]
    }),
    actions: {
        setDate(date){
            this.dates = [new Date(date[0]), new Date(date[1])]
        }
    },
    persist: {
        enabled: true,
        strategies:[
            {
                key:"filter",
                storage: localStorage
            },
        ]
    }
})