<template>
    <router-link :to="to"
        exact-active-class="bg-custom-100 hover:!bg-custom-100 !text-custom-500 hover:!text-custom-500"
        :class="['text-gray-800 hover:bg-gray-100 hover:text-gray-900 group w-full flex items-center px-2 py-3 text-tiny font-medium rounded-md']">
        <span class="material-symbols-outlined text-[22px] mr-2">
            arrow_back
        </span>
        {{ text }}
    </router-link>
</template>

<script>
export default{
    props: ['to', 'text']
}
</script>
