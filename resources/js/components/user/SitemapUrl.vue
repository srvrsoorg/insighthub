<template>
    <Table :head="sitemapHits.thead" :bodyHeight="'max-h-[310px] min-h-[100px]'">
        <template #header>
            <TableTitle :tableTitle="'Sitemap URL Activity'" :tooltip="'Sitemap a roadmap for search engines to navigate your site efficiently. See recent sitemap activity – user agents, methods, and statuses. Crucial for site health, error fixes, and keeping your sitemap in top shape.'"/>
        </template>
        <template v-if="sitemapHits.data.length && !sitemapHits.refreshing && !loading">
            <tr class="even:bg-slate-50 last:border-0 border-b border-gray-300" v-for="(data, key) in sitemapHits.data" :key="key">
                <td class="px-4 py-3.5 text-sm text-left" v-if="!$route.params.application">
                    {{ data.application_name }}
                </td>
                <td class="px-4 py-3.5 text-sm text-left max-w-[300px] overflow-auto truncate" v-tooltip="data.url">
                    {{ data.url }}
                </td>
                <td class="px-4 py-3.5 text-sm text-left">
                    {{ data.bot_name == '0' ? 'Other' : data.bot_name }}
                </td>
                <td class="px-4 py-3.5 text-sm text-left">
                    {{ data.method }}
                </td>
                <td class="px-4 py-3.5 text-sm text-left">
                    {{ data.status }}
                </td>
                <td class="px-4 py-3.5 text-sm text-left">
                    {{ data.created_at }}
                </td>
            </tr>
        </template>
        <template v-else>
            <template v-if="sitemapHits.refreshing || loading">
                <tr v-for="i in 5" :key="i">
                    <td class="whitespace-nowrap py-2 px-4 text-sm" v-for="j in sitemapHits.thead.length" :key="j">
                        <Skeleton :count="1"/>
                    </td>
                </tr>
            </template>
            <tr v-else>
                <td :colspan="sitemapHits.thead.length" class="text-center text-sm px-6 py-4">
                    No Data Found
                </td>
            </tr>
        </template>
        <template #pagination>
            <div v-if="sitemapHits.data.length && !sitemapHits.refreshing && !loading">
                <PerPage v-model="sitemapHits.per_page" @change="$emit('changePage')"/>
            </div>
        </template>
    </Table>
</template>

<script>
export default {
    props: ['sitemapHits']
}
</script>