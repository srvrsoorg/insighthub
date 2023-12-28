<template>
    <Table :head="referrerWithBandwidth.thead" :bodyHeight="'max-h-[310px] min-h-[100px]'">
        <template #header>
            <div class="bg-violet-950 bg-opacity-10 py-2 px-4 border-b border-zinc-300">
                <span class="text-lg flex items-center gap-2">
                    High Bandwidth Usage Referrers
                    <span class="material-symbols-outlined text-base mt-0.5 cursor-default" v-tooltip.right="'See which websites or links are directing the most traffic to your specific application, potentially impacting bandwidth usage.'">
                        info
                    </span>
                </span>
            </div>
        </template>
        <template v-if="referrerWithBandwidth.data.length && !referrerWithBandwidth.refreshing && !loading">
            <tr class="even:bg-slate-50 last:border-0 border-b border-gray-300" v-for="(data, key) in referrerWithBandwidth.data" :key="key">
                <td class="px-4 py-3.5 text-sm text-left" v-if="!$route.params.application">
                    {{ data.application_name }}
                </td>
                <td class="px-4 py-3.5 text-sm text-left  max-w-[200px] overflow-auto truncate">
                    <span v-tooltip="data.referrer_url">{{ data.referrer_url }}</span>
                </td>
                <td class="px-4 py-3.5 text-sm text-left">
                    {{ data.bandwidth_in_MB }} MB
                </td>
                <td class="px-4 py-3.5 text-sm text-left">
                    {{ data.total_count }}
                </td>
            </tr>
        </template>
        <template v-else>
            <template v-if="referrerWithBandwidth.refreshing || loading">
                <tr v-for="i in 5" :key="i">
                    <td class="whitespace-nowrap py-2 px-4 text-sm" v-for="j in referrerWithBandwidth.thead.length" :key="j">
                        <Skeleton :count="1"/>
                    </td>
                </tr>
            </template>
            <tr v-else>
                <td :colspan="referrerWithBandwidth.thead.length" class="text-center text-sm px-6 py-4">
                    No Data Found
                </td>
            </tr>
        </template>
        <template #pagination>
            <div v-if="referrerWithBandwidth.data.length && !referrerWithBandwidth.refreshing && !loading">
                <PerPage v-model="referrerWithBandwidth.per_page" @change="$emit('changePage')"/>
            </div>
        </template>
    </Table>
</template>

<script>
export default {
    props: ['referrerWithBandwidth'],
}
</script>
