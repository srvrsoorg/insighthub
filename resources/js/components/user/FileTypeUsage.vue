<template>
    <Table :head="fileTypes.thead" :bodyHeight="'max-h-[310px] min-h-[100px]'">
        <template #header>
            <div class="bg-violet-950 bg-opacity-10 py-2 px-4 border-b border-zinc-300">
                <span class="text-lg flex items-center gap-2">
                    File Type Usage Breakdown
                    <span class="material-symbols-outlined text-base mt-0.5 cursor-default" v-tooltip.right="'See which types of files are using the most space and getting the most attention.'">
                        info
                    </span>
                </span>
            </div>
        </template>
        <template v-if="fileTypes.data.length && !fileTypes.refreshing && !loading">
            <tr class="even:bg-slate-50 last:border-0 border-b border-gray-300" v-for="(data, key) in fileTypes.data" :key="key">
                <td class="px-4 py-3.5 text-sm text-left" v-if="!$route.params.application">
                    {{ data.server_name }}
                </td>
                <td class="px-4 py-3.5 text-sm text-left">
                    {{ data.document_type }}
                </td>
                <td class="px-4 py-3.5 text-sm text-left">
                    {{ data.total_bandwidth_MB }} MB
                </td>
                <td class="px-4 py-3.5 text-sm text-left">
                    {{ data.total_count }}
                </td>
            </tr>
        </template>
        <template v-else>
            <template v-if="fileTypes.refreshing || loading">
                <tr v-for="i in 5" :key="i">
                    <td class="whitespace-nowrap py-2 px-4 text-sm" v-for="j in fileTypes.thead.length" :key="j">
                        <Skeleton :count="1"/>
                    </td>
                </tr>
            </template>
            <tr v-else>
                <td :colspan="fileTypes.thead.length" class="text-center text-sm px-6 py-4">
                    No Data Found
                </td>
            </tr>
        </template>
        <template #pagination>
            <div v-if="fileTypes.data.length && !fileTypes.refreshing && !loading">
                <PerPage v-model="fileTypes.per_page" @change="$emit('changePage')"/>
            </div>
        </template>
    </Table>
</template>

<script>
export default {
    props: ['fileTypes']
}
</script>
