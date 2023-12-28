<template>
    <div class="flex items-center justify-center">
        <span :class="[isLightColor ? 'text-custom-600' : 'text-custom-500', 'material-symbols-outlined bg-gray-100 rounded-full px-2 py-1 w-fit text-2xl']">
            {{statIcon}}
        </span>
    </div>
    <div class="text-center mt-2">
        <span class="block">{{ title }}</span>
        <div class="flex flex-col mt-1">
            <span class="text-gray-500 truncate break-all text-sm" :class="customClass" v-tooltip="content">
                {{ content }}
            </span>
        </div>
    </div>
</template>

<script>
export default {
    props:{
        title:{
            type: String,
            required: true
        },
        statIcon: {
            type: String,
            required: true
        },
        content:{
            required: true
        },
        customClass:{
            required: false,
            default: []
        }   
    }
}
</script>