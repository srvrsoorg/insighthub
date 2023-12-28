<template>
    <div class="text-end py-3">
        <label class="text-sm mr-1 text-gray-500">Number of Rows: </label>
        <select v-model="per_page" class="text-sm border-1 border-gray-300 text-gray-700 focus:ring-0 focus:border-sa-500 rounded-md py-1 pr-7 pl-2 focus:outline-none">
            <option value="5">5</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="500">500</option>
        </select>
    </div>
</template>

<script>
export default{
    props: {
        modelValue: {
            type: String,
            required: true
        }
    },
    computed:{
        per_page:{
            get(){
                return this.modelValue
            },
            set(value){
                this.$emit('update:modelValue', value)
            }
        }
    }
}
</script>
