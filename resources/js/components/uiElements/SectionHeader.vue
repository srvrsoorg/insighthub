<template>
    <div :class="[isLightColor ? 'border-custom-600' : 'border-custom-500', 'bg-violet-950 bg-opacity-5 w-fit text-lg border-x-4 px-4 py-2']">
        {{ title }}
    </div>
</template>

<script>
export default {
    props: ['title']
}
</script>