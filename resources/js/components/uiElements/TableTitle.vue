<template>
    <div class="bg-violet-950 bg-opacity-10 py-2 px-4 border-b border-zinc-300">
        <span class="text-lg flex items-center gap-2">
            {{ tableTitle }}
            <span v-if="tooltip" class="material-symbols-outlined text-base mt-0.5 cursor-default" v-tooltip.right="tooltip">
                info
            </span>
        </span>
    </div>
</template>

<script>
export default {
    props:{
        tableTitle:{
            type: String,
            required: true
        },
        tooltip:{
            type: String,
            required: false
        }
    }
}
</script>