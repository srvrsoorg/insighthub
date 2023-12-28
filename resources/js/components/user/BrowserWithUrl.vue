<template>
    <Table :head="url.thead" :bodyHeight="'max-h-[310px] min-h-[100px]'">
        <template #header>
            <div class="bg-violet-950 bg-opacity-10 py-2 px-4 border-b border-zinc-300">
                <span class="text-lg flex items-center gap-2">
                    Browser Hits on URLs
                    <span class="material-symbols-outlined text-base mt-0.5 cursor-default" v-tooltip.right="'Gain insights into user browser preferences and optimize your application accordingly with this breakdown of URL access by browser.'">
                        info
                    </span>
                </span>
            </div>
        </template>
        <template v-if="url.data.length && !url.refreshing && !loading">
            <tr class="even:bg-slate-50 last:border-0 border-b border-gray-300" v-for="(data, key) in url.data" :key="key">
                <td class="px-4 py-3.5 text-sm text-left" v-if="!$route.params.application">
                    {{ data.application_name }}
                </td>
                <td class="px-4 py-3.5 text-sm text-left">
                    {{data.browser == '0' ? 'Other' : data.browser}}
                </td>
                <td class="px-4 py-3.5 text-sm text-left  max-w-[250px] overflow-auto truncate">
                    <span v-tooltip="data.url">{{ data.url }}</span>
                </td>
                <td class="px-4 py-3.5 text-sm text-left">
                    {{ data.total_count }}
                </td>
            </tr>
        </template>
        <template v-else>
            <template v-if="url.refreshing || loading">
                <tr v-for="i in 5" :key="i">
                    <td class="whitespace-nowrap py-2 px-4 text-sm" v-for="j in url.thead.length" :key="j">
                        <Skeleton :count="1"/>
                    </td>
                </tr>
            </template>
            <tr v-else>
                <td :colspan="url.thead.length" class="text-center text-sm px-6 py-4">
                    No Data Found
                </td>
            </tr>
        </template>
        <template #pagination>
            <div v-if="url.data.length && !url.refreshing && !loading">
                <PerPage v-model="url.per_page" @change="$emit('changePage')"/>
            </div>
        </template>
    </Table>
</template>

<script>
export default {
    props: ['url']
}
</script>
