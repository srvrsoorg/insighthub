<template>
    <div class="container-fluid h-full">
        <div class="flex flex-wrap gap-3 justify-between items-center">
            <h1 class="text-2xl text-gray-900">Dashboard</h1>
            <div class="flex sm:flex-nowrap flex-wrap gap-4 ">
                <BotSwitch v-model="isBotRequest"/>
                <DateFilter />
            </div>
        </div>
        <div class="grid md:grid-cols-3 grid-cols-1 gap-5 mt-6">
            <div class="grid md:grid-cols-1 sm:grid-cols-2 grid-cols-1 gap-x-6 gap-y-5">
                <div :class="['bg-white rounded-lg px-4 py-2']">
                    <div class="flex justify-between items-center h-full" v-if="overview && !loading">
                        <div class="flex flex-col gap-1.5">
                            <span>Total Servers</span>
                            <span class="text-gray-500 text-2xl font-cera-pro-light">{{ overview.serverCount }}</span>
                        </div>
                        <div class="bg-custom-500 rounded-full px-2 py-1 flex justify-center items-center">
                            <span :class="[textColorClass, 'material-symbols-outlined text-2xl']">
                                dns
                            </span>
                        </div>
                    </div>
                    <Skeleton v-else :count="3"/>
                </div>
                <div :class="['bg-white rounded-lg px-4 py-2']">
                    <div class="flex justify-between items-center h-full" v-if="overview && !loading">
                        <div class="flex flex-col gap-1.5">
                            <span>Total Applications</span>
                            <span class="text-gray-500 text-2xl font-cera-pro-light">{{ overview.applicationCount }}</span>
                        </div>
                        <div class="bg-custom-500 rounded-full px-2 py-1 flex justify-center items-center">
                            <span :class="[textColorClass, 'material-symbols-outlined text-2xl']">
                                deployed_code
                            </span>
                        </div>
                    </div>
                    <Skeleton v-else :count="3"/>
                </div>
            </div>
            <div class="md:col-span-2 col-span-1">
                <div class="grid sm:grid-cols-2 grid-cols-1 gap-5">
                    <div :class="[isLightColor ? 'border-custom-600' : 'border-custom-500', 'bg-white border-b-4 rounded-sm p-4']">
                        <template v-if="Object.keys(countByWebserver.data).length && !countByWebserver.refreshing && !loading">
                            <div class="flex justify-between items-center">
                                <span class="text-xl">Servers</span>
                                <div class="bg-custom-500 rounded-full px-1.5 py-0.5 flex justify-center items-center">
                                    <span :class="[textColorClass, 'material-symbols-outlined text-xl']">
                                        dns
                                    </span>
                                </div>
                            </div>
                            <div class="flex justify-between items-center text-tiny text-gray-500 hover:text-custom-500 px-2 mb-3 mt-5">
                                <span>Apache</span>
                                <span>
                                    {{countByWebserver.data.apache2 ? countByWebserver.data.apache2.server_count : 0}}
                                </span>
                            </div>
                            <div class="flex justify-between items-center text-tiny text-gray-500 hover:text-custom-500 px-2 my-4">
                                <span>Nginx</span>
                                <span>
                                    {{countByWebserver.data.nginx ? countByWebserver.data.nginx.server_count : 0}}
                                </span>
                            </div>
                            <div class="flex justify-between items-center text-tiny text-gray-500 hover:text-custom-500 px-2 mt-3">
                                <span>OpenLiteSpeed</span>
                                <span>
                                    {{countByWebserver.data.openlitespeed ? countByWebserver.data.openlitespeed.server_count : 0}}
                                </span>
                            </div>
                        </template>
                        <Skeleton v-else :count="7"/>
                    </div>
                    <div :class="[isLightColor ? 'border-custom-600' : 'border-custom-500', 'bg-white border-b-4 rounded-sm p-4']">
                        <template v-if="Object.keys(countByWebserver.data).length && !countByWebserver.refreshing && !loading">
                            <div class="flex justify-between items-center">
                                <span class="text-xl">Applications</span>
                                <div class="bg-custom-500 rounded-full px-1.5 py-0.5 flex justify-center items-center">
                                    <span :class="[textColorClass, 'material-symbols-outlined text-xl']">
                                        deployed_code
                                    </span>
                                </div>
                            </div>
                            <div class="flex justify-between items-center text-tiny text-gray-500 hover:text-custom-500 px-2 mb-3 mt-5">
                                <span>Apache</span>
                                <span>
                                    {{countByWebserver.data.apache2 ? countByWebserver.data.apache2.application_count : 0}}
                                </span>
                            </div>
                            <div class="flex justify-between items-center text-tiny text-gray-500 hover:text-custom-500 px-2 my-4">
                                <span>Nginx</span>
                                <span>
                                    {{countByWebserver.data.nginx ? countByWebserver.data.nginx.application_count : 0}}
                                </span>
                            </div>
                            <div class="flex justify-between items-center text-tiny text-gray-500 hover:text-custom-500 px-2 mt-3">
                                <span>OpenLiteSpeed</span>
                                <span>
                                    {{countByWebserver.data.openlitespeed ? countByWebserver.data.openlitespeed.application_count : 0}}
                                </span>
                            </div>
                        </template>
                        <Skeleton v-else :count="7"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid 2xl:grid-cols-3 grid-cols-1 gap-5 mt-6">
            <!-- Top High Memory Usage Servers -->
            <div class="bg-white rounded-lg px-4 py-3">
                <span class="text-lg">
                    Memory-Intensive Servers
                </span>
                <MemoryIntensiveServer v-if="memoryUsage.data && !memoryUsage.refreshing && !loading" :chartData="memoryUsage.data"/>
                <Skeleton v-else :count="10"/>
            </div>
            
            <!-- Top High Disk Usage Servers -->
            <div class="bg-white rounded-lg px-4 py-3">
                <span class="text-lg">
                    Disk-Intensive Servers
                </span>
                <DiskIntensiveServer v-if="diskUsage.data && !memoryUsage.refreshing && !loading" :chartData="diskUsage.data"/>
                <Skeleton v-else :count="10"/>
            </div>
            <!-- Top High Load Servers -->
            <div class="bg-white rounded-lg px-4 py-3">
                <span class="text-lg">
                    High Load Servers
                </span>
                <HighLoadServer v-if="highLoad.data && !memoryUsage.refreshing && !loading" :chartData="highLoad.data"/>
                <Skeleton v-else :count="10"/>
            </div>
        </div>
        <div class="grid xl:grid-cols-2 grid-cols-1 gap-5 mt-6">
            <!-- Most Visited Pages -->
            <MostVisitedPages :mostVisitedPages="mostVisitedPages" @changePage="setApi(); fetchMostVisitedPages(api.most_visited_pages)"/>
            
            <!--  Bandwidth Consumer Servers -->
            <Table :head="bandwidthConsumerServers.thead" :bodyHeight="'max-h-[310px] min-h-[100px]'">
                <template #header>
                    <div class="bg-violet-950 bg-opacity-10 py-2 px-4 border-b border-zinc-300">
                        <span class="text-lg">
                            Bandwidth-Intensive Servers
                        </span>
                    </div>
                </template>
                <template v-if="bandwidthConsumerServers.data.length && !bandwidthConsumerServers.refreshing && !loading">
                    <tr class="even:bg-slate-50 last:border-0 border-b border-gray-300" v-for="(data, key) in bandwidthConsumerServers.data" :key="key">
                        <td class="px-4 py-3.5 text-sm text-left">
                            {{ data.server_name }}
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
                    <template v-if="bandwidthConsumerServers.refreshing || loading">
                        <tr v-for="i in 5" :key="i">
                            <td class="whitespace-nowrap py-2 px-4" v-for="j in 3" :key="j">
                                <Skeleton :count="1"/>
                            </td>
                        </tr>
                    </template>
                    <tr v-else>
                        <td colspan="3" class="text-center text-sm px-6 py-4">
                            No Data Found
                        </td>
                    </tr>
                </template>
                <template #pagination>
                    <div v-if="bandwidthConsumerServers.data.length && !bandwidthConsumerServers.refreshing && !loading">
                        <PerPage v-model="bandwidthConsumerServers.per_page" @change="fetchBandwidthConsumerServers"/>
                    </div>
                </template>
            </Table>
        </div>
        <div class="grid xl:grid-cols-2 grid-cols-1 gap-5 mt-6">
            <div class="bg-white rounded-lg px-4 py-3">
                <span class="text-lg">
                    Distribution of 4xx Errors Across Applications
                </span>
                <Errors :chartData="errors.client.chartData" v-if="errors.client.chartData && !errors.client.refreshing && !loading"/>
                <Skeleton :count="10" v-else/>
            </div>
            <div class="bg-white rounded-lg px-4 py-3">
                <span class="text-lg">
                    Distribution of 5xx Errors Across Applications
                </span>
                <Errors :chartData="errors.server.chartData" v-if="errors.server.chartData && !errors.server.refreshing && !loading"/>
                <Skeleton :count="10" v-else/>
            </div>
        </div>
        <div class="mt-6">
            <!-- Sitemap URL Hits with User Agent, Method, Status & Timestamp -->
            <SitemapUrl :sitemapHits="sitemapHits" @changePage="setApi(); fetchSitemapHits(api.sitemap_hits)"/>
        </div>
        <div class="grid xl:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-5 mt-6">
            <div class="xl:col-span-2 col-span-1">
                <!-- File Types with Bandwidth Usage -->
                <FileTypeUsage :fileTypes="fileTypes" @changePage="fetchTopFileTypes"/>
            </div>
            <div class="bg-white rounded-lg px-4 py-3">
                <!-- File Types Pie Chart -->
                <span class="text-lg">
                    Popular File Types
                </span>
                <FileTypesChart :chartData="fileTypes.chartData" v-if="fileTypes.chartData && !fileTypes.chartRefreshing && !loading"/>
                <Skeleton :count="12" v-else/>
            </div>
        </div>
        <div class="mt-6">
            <!--  Bandwidth Consume URL with Mime Type & Bandwidth Usage -->
            <HighBandwidthUrl :bandwidthConsumerUrl="bandwidthConsumerUrl" @change="setApi(); fetchBandwidthConsumerUrl(api.bandwidth_cunsume_url)"/>
        </div>
    </div>
</template>

<script>
import apiMixin from '@/mixins/commonApi'
import { defineAsyncComponent } from 'vue'

export default{
    mixins: [apiMixin],
    components:{
        FileTypesChart: defineAsyncComponent(() => import('@/components/charts/mainDashboard/FileTypes.vue')),
        MemoryIntensiveServer: defineAsyncComponent(() => import('@/components/charts/mainDashboard/MemoryIntensive.vue')),
        DiskIntensiveServer: defineAsyncComponent(() => import('@/components/charts/mainDashboard/DiskIntensive.vue')),
        HighLoadServer: defineAsyncComponent(() => import('@/components/charts/mainDashboard/HighLoad.vue')),
        HighTrafficApps: defineAsyncComponent(() => import('@/components/charts/common/HighTrafficApps.vue')),
        Errors: defineAsyncComponent(() => import('@/components/charts/mainDashboard/Errors.vue')),
        SitemapUrl: defineAsyncComponent(() => import('@/components/user/SitemapUrl.vue')),
        HighBandwidthUrl: defineAsyncComponent(() => import('@/components/user/HighBandwidthUrl.vue')),
        FileTypeUsage: defineAsyncComponent(() => import('@/components/user/FileTypeUsage.vue')),
        MostVisitedPages: defineAsyncComponent(() => import('@/components/user/MostVisitedPages.vue')),
    },
    data(){
        return{
            api: {},
            overview: null,
            countByWebserver: {
                thead: ['Web Server', 'Apache', 'Nginx', 'OLS'],
                data: {},
                refreshing: false
            },
            memoryUsage: {
                data: null,
                thead: ['Server', 'Memory Usage'],
                refreshing: false
            },
            diskUsage: {
                data: null,
                thead: ['Server', 'Disk Usage'],
                refreshing: false
            },
            highLoad: {
                data: null,
                thead: ['Server', 'Load'],
                refreshing: false
            },
            fileTypes: {
                data: [],
                per_page: '5',
                chartData: null,
                thead: ['Server', 'File Type', 'Bandwidth Usage', 'Hits'],
                refreshing: false,
                chartRefreshing: false
            },
            bandwidthConsumerServers: {
                data: [],
                thead: ['Server', 'Bandwidth Usage', 'Hits'],
                refreshing: false,
                per_page: '5'
            },
            bandwidthConsumerUrl: {
                data: [],
                per_page: '5',
                thead: [
                    'Application', 
                    'URL', 
                    {title: 'Mime Type', tooltip: 'Indicates the type of content or file format associated with this URL.'}, 
                    'Bandwidth Usage'
                ],
                refreshing: false
            },
            sitemapHits: {
                data: [],
                per_page: '5',
                pagination: null,
                thead: ['Application', 'Sitemap URL', 'Bot Name', 'Method', 'Status', 'Timestamp'],
                refreshing: false
            },
            mostVisitedPages:{
                data: [],
                refreshing: false,
                per_page: '5',
                thead: [
                    'Application', 
                    {title: 'Title', tooltip: "Displays the title of the web page as it appears in the browser's title bar or tab. Can be empty if a title is not set."}, 
                    {title: 'URL', tooltip: 'The full path or address of the web page, including protocol and domain.'}, 
                    {title: 'Hits', tooltip: 'Represents the total number of times this page has been accessed during the specified time period. Indicates its popularity and user engagement.'}
                ],
            },
            errors:{
                server:{
                    chartData: null,
                    refreshing: false
                },
                client:{
                    chartData: null,
                    refreshing: false
                }
            }
        }
    },
    methods: {
        setApi(){
            return new Promise((resolve) => {
                this.api = {
                    bandwidth_cunsume_url: `/bandwidths/top-urls?per_page=${this.bandwidthConsumerUrl.per_page}&${this.queryParam}`,
                    sitemap_hits: `specific-urls/is_sitemap_url?per_page=${this.sitemapHits.per_page}&${this.queryParam}`,
                    most_visited_pages: `/urls?per_page=${this.mostVisitedPages.per_page}&${this.queryParam}`
                }
                resolve();
            })      
        },
        fetchData(){
            this.fetchOverview(),
            this.fetchUsageData('memory_in_pr', 'memoryUsage'),
            this.fetchUsageData('disk_in_pr', 'diskUsage'),
            this.fetchUsageData('fifteen_min_load', 'highLoad'),
            this.fetchBandwidthConsumerServers(),
            this.fetchTopFileTypes(),
            this.fetchFileTypesPieChart(),
            this.fetchBandwidthConsumerUrl(this.api.bandwidth_cunsume_url),
            this.fetchSitemapHits(this.api.sitemap_hits),
            this.fetchMostVisitedPages(this.api.most_visited_pages),
            this.fetchErrorChart('4xx', 'client'),
            this.fetchErrorChart('5xx', 'server')
        },
        async fetchOverview(){
            await this.$axios.get(`/overview`).then(({data}) => {
                this.overview = data
                data.webserverData.forEach(item => {
                    const { web_server, server_count, application_count } = item;
                    this.countByWebserver['data'][web_server] = { server_count, application_count };
                });
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            })
        },
        async fetchUsageData(type, dataProperty){
            this[dataProperty].refreshing = true
            let url = `/servers/high-usages/${type}`

            await this.$axios.get(url).then(({data}) => {
                this[dataProperty].data = data.usages
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this[dataProperty].refreshing = false
            })
        },
        async fetchBandwidthConsumerServers(){
            this.bandwidthConsumerServers.refreshing = true
            await this.$axios.get(`/bandwidths/server-statistics?per_page=${this.bandwidthConsumerServers.per_page}&${this.queryParam}`).then(({data}) => {
                this.bandwidthConsumerServers.data = data.serverStatistics
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.bandwidthConsumerServers.refreshing = false
            })
        },
        async fetchTopFileTypes(){
            this.fileTypes.refreshing = true
            await this.$axios.get(`/bandwidths/top-document-types?per_page=${this.fileTypes.per_page}&${this.queryParam}`).then(({data}) => {
                this.fileTypes.data = data.documentTypeStatistics
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.fileTypes.refreshing = false
            })
        },
        async fetchFileTypesPieChart(){
            this.fileTypes.chartRefreshing = true
            await this.$axios.get(`/document-types/pie-chart?${this.queryParam}`).then(({data}) => {
                this.fileTypes.chartData = data
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.fileTypes.chartRefreshing = false
            })
        },
        async fetchErrorChart(type, dataProperty){
            this.errors[dataProperty].refreshing = true
            await this.$axios.get(`/statuses/pie-chart/${type}?${this.queryParam}`).then(({data}) => {
                this.errors[dataProperty].chartData = data
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.errors[dataProperty].refreshing = false
            })
        }
    }
}
</script>

