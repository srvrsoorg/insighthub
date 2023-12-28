<template>
    <Table :head="ipWithBandwidth.thead" :bodyHeight="'max-h-[310px] min-h-[100px]'">
        <template #header>
            <div class="bg-violet-950 bg-opacity-10 py-2 px-4 border-b border-zinc-300">
                <span class="text-lg flex items-center gap-2">
                    High Bandwidth Usage IPs
                    <span class="material-symbols-outlined text-base mt-0.5 cursor-default" v-tooltip.right="' Identify top data-consuming IP addresses for website optimization. Analyze user activity, manage costs, and troubleshoot coding issues or unusual traffic patterns.'">
                        info
                    </span>
                </span>
            </div>
        </template>
        <template v-if="ipWithBandwidth.data.length && !ipWithBandwidth.refreshing && !loading">
            <tr class="even:bg-slate-50 last:border-0 border-b border-gray-300" v-for="(data, key) in ipWithBandwidth.data" :key="key">
                <td class="px-4 py-3.5 text-sm text-left" v-if="!$route.params.application">
                    {{ data.application_name }}
                </td>
                <td class="px-4 py-3.5 text-sm text-left">
                    {{data.ip}}
                </td>
                <td class="px-4 py-3.5 text-sm text-left">
                    {{ data.bandwidth_in_MB }} MB
                </td>
                <td class="px-4 py-3.5 text-sm text-left">
                    {{ data.total_count }}
                </td>
                <td class="px-4 py-3.5 text-sm text-left">
                    {{ data.last_visit }}
                </td>
            </tr>
        </template>
        <template v-else>
            <template v-if="ipWithBandwidth.refreshing || loading">
                <tr v-for="i in 5" :key="i">
                    <td class="whitespace-nowrap py-2 px-4 text-sm" v-for="j in ipWithBandwidth.thead.length" :key="j">
                        <Skeleton :count="1"/>
                    </td>
                </tr>
            </template>
            <tr v-else>
                <td :colspan="ipWithBandwidth.thead.length" class="text-center text-sm px-6 py-4">
                    No Data Found
                </td>
            </tr>
        </template>
        <template #pagination>
            <div v-if="ipWithBandwidth.data.length && !ipWithBandwidth.refreshing && !loading">
                <PerPage v-model="ipWithBandwidth.per_page" @change="$emit('changePage')"/>
            </div>
        </template>
    </Table>
</template>

<script>
export default {
    props: ['ipWithBandwidth']
}
</script>
