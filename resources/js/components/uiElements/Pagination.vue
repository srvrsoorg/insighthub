<template>
    <TailwindPagination
        class="flex-wrap"
        :active-classes="['bg-custom-500 border-custom-500 !px-3 !py-2', textColorClass]"
        :item-classes="['bg-white text-gray-500 border-gray-300 first:rounded-l-md last:rounded-r-md !px-3 !py-2']"
        :limit="1"
        :data="pagination"
        @pagination-change-page="changePage"
    >
        <template #prev-nav>
            <i class="fas fa-angle-left"></i>
        </template>
        <template #next-nav>
            <i class="fas fa-angle-right"></i>
        </template>
    </TailwindPagination>
</template>

<script>
import { TailwindPagination } from 'laravel-vue-pagination';

export default{
    props: ['pagination'],
    components:{TailwindPagination},
    methods:{
        changePage(value){
            this.$emit('page-change', value)
        }
    }
}
</script>
