<template>
    <Listbox as="div" v-model="isBotRequest">
        <ListboxLabel class="sr-only">Change published status</ListboxLabel>
        <div class="relative">
            <ListboxButton class="inline-flex items-center gap-3 rounded-md bg-custom-500 px-3 py-2 focus:outline-none">
                <p :class="[textColorClass,'text-sm font-semibold']">{{ title }}</p>
                <span class="sr-only">Change published status</span>
                <ChevronDownIcon :class="[textColorClass,'h-5 w-5']" aria-hidden="true" />
            </ListboxButton>

            <transition leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100" leave-to-class="opacity-0">
                <ListboxOptions class="absolute right-0 z-10 mt-2 w-20 origin-top-right divide-y divide-gray-200 overflow-hidden rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                    <ListboxOption as="template" v-for="option in filterOptions" :key="option.title" :value="option.value" v-slot="{ active }">
                        <li :class="[active ? `bg-custom-500 ${ textColorClass }` : `text-gray-900 ${ textHoverClass }`, 'cursor-pointer select-none px-4 py-2 text-sm']">
                            <div class="flex flex-col">
                                <div class="flex justify-between">
                                    <p class="font-normal">{{ option.title }}</p>
                                </div>
                            </div>
                        </li>
                    </ListboxOption>
                </ListboxOptions>
            </transition>
        </div>
    </Listbox>
</template>

<script>
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from '@headlessui/vue'
import { ChevronDownIcon } from '@heroicons/vue/20/solid'
export default {
    props: {
        value: {
            type: Boolean,
            required: true
        }
    },
    components: {
       Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions, ChevronDownIcon
    },
    data(){
        return{
            isBotRequest: '',
            filterOptions: [
                {title: 'All', value: ''},
                {title: 'Bots', value: '1'},
                {title: 'Visitors', value: '0'}
            ]
        }
    },
    computed:{
        title(){
            let selected = this.filterOptions.find(option => option.value == this.isBotRequest)
            return selected.title
        }
    },
    watch: {
        isBotRequest(val) {
            this.$emit("update:modelValue", val);
        },
        value(val) {
            this.isBotRequest = val ? 1 : 0;
        }
    }
};
</script>
