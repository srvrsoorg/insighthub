<template>
    <div role="status" class="w-full animate-pulse my-2" v-for="n in count" :key="n">
        <div class="rounded-md w-full" :class="[height, bgColor]"></div>
    </div>
</template>

<script>
    export default{
        props: {
            count: {
                type: Number,
                required: true
            },
            height: {
                type: String,
                default: 'h-4'
            },
            bgColor:{
                type: String,
                required: false,
                default: 'bg-gray-200'
            }
        }
    }
</script>