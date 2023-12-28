<template>
    <Table :head="mostVisitedPages.thead" :bodyHeight="'max-h-[310px] min-h-[100px]'">
        <template #header>
            <div class="bg-violet-950 bg-opacity-10 py-2 px-4 border-b border-zinc-300">
                <span class="text-lg flex items-center gap-2">
                    Most Visited Pages
                    <span class="material-symbols-outlined text-base mt-0.5 cursor-default" v-tooltip.right="'Highlights the pages with the highest user engagement across specific applications.'">
                        info
                    </span>
                </span>
            </div>
        </template>
        <template v-if="mostVisitedPages.data.length && !mostVisitedPages.refreshing && !loading">
            <tr class="even:bg-slate-50 last:border-0 border-b border-gray-300" v-for="(data, key) in mostVisitedPages.data" :key="key">
                <td class="px-4 py-3.5 text-sm text-left max-w-[100px] overflow-auto truncate" v-if="!$route.params.application">
                    <span v-tooltip="data.application_name">{{ data.application_name }}</span>
                </td>
                <td class="px-4 py-3.5 text-sm text-left max-w-[100px] overflow-auto truncate" v-tooltip="data.title">
                    {{ data.title ?? '-' }}
                </td>
                <td class="px-4 py-3.5 text-sm text-left  max-w-[200px] overflow-auto truncate">
                    <span v-tooltip="data.url">{{ data.url }}</span>
                </td>
                <td class="px-4 py-3.5 text-sm text-left">
                    {{ data.total_count }}
                </td>
            </tr>
        </template>
        <template v-else>
            <template v-if="mostVisitedPages.refreshing || loading">
                <tr v-for="i in 5" :key="i">
                    <td class="whitespace-nowrap py-2 px-4" v-for="j in mostVisitedPages.thead.length" :key="j">
                        <Skeleton :count="1"/>
                    </td>
                </tr>
            </template>
            <tr v-else>
                <td :colspan="mostVisitedPages.thead.length" class="text-center text-sm px-6 py-4">
                    No Data Found
                </td>
            </tr>
        </template>
        <template #pagination>
            <div v-if="mostVisitedPages.data.length && !mostVisitedPages.refreshing && !loading">
                <PerPage v-model="mostVisitedPages.per_page" @change="$emit('changePage')"/>
            </div>
        </template>
    </Table>
</template>

<script>
export default {
    props: ['mostVisitedPages']
}
</script>
