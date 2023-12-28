<template>
    <button type="button" @click="togglePasswordVisibility" class="absolute inset-y-0 right-0 flex items-center pr-3">
        <span class="material-symbols-outlined text-tiny text-neutral-500 bg-white pl-3" v-if="showPassword">
            visibility_off
        </span>
        <span class="material-symbols-outlined text-tiny text-neutral-500 bg-white pl-3" v-else>
            visibility
        </span>
    </button>
</template>

<script>
export default{
    props: {
        showPassword:{
            type: Boolean,
            default: false
        }
    },
    methods: {
        togglePasswordVisibility() {
            this.$emit('toggle');
        }
    }
}
</script>
