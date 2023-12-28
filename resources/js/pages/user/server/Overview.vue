<template>
    <div class="container-fluid">
        <div class="flex flex-wrap gap-3 justify-between items-center">
            <h1 class="text-2xl text-gray-900">Server Dashboard</h1>
            <div class="flex sm:flex-nowrap flex-wrap gap-5 ">
                <BotSwitch v-model="isBotRequest" />
                <DateFilter />
            </div>
        </div>
        <SectionHeader title="Overview" class="mt-6"/>
        <ServerOverview :server="server"/>
        <div class="grid xl:grid-cols-2 grid-cols-1 gap-5 mt-5">
            <div class="bg-white rounded-lg">
                <template v-if="server">
                    <div class="flex justify-between items-center gap-5 px-5 py-3 border-b border-slate-200">
                        <div class="flex items-center justify-center gap-3">
                            <div class="w-16 text-center">
                                <span :class="[isLightColor ? 'text-custom-600' : 'text-custom-500', 'material-symbols-outlined text-3xl']">
                                    deployed_code
                                </span>
                            </div>
                            <span>Applications</span>
                        </div>
                        <span class="text-gray-500">{{ server.applicationCount }}</span>
                    </div>
                    <div class="flex justify-between items-center gap-5 px-5 py-4 border-b border-slate-200">
                        <div class="flex items-center gap-3">
                            <div class="w-16">
                                <img
                                    src="/svg/ubuntu.svg"
                                    class="w-8 h-auto mx-auto"
                                />
                            </div>
                            <span>OS</span>
                        </div>
                        <span class="text-tiny text-gray-500">Ubuntu {{ server.version }}</span>
                    </div>
                    <div class="flex justify-between items-center gap-5 px-5 py-4 border-b border-slate-200">
                        <div class="flex items-center gap-3">
                            <div class="w-16">
                                <img
                                    v-if="server.web_server == 'nginx'"
                                    src="/svg/web-servers/nginx.svg"
                                    class="w-8 h-auto mx-auto"
                                />
                                <img
                                    v-else-if="server.web_server == 'openlitespeed'"
                                    src="/svg/web-servers/ols.svg"
                                    class="w-9 h-auto mx-auto"
                                />
                                <img
                                    v-else
                                    src="/svg/web-servers/apache.svg"
                                    class="w-8 h-auto mx-auto"
                                />
                            </div>
                            <span>Web Server</span>
                        </div>
                        <span class="text-tiny text-gray-500 capitalize">
                            {{ server.web_server == 'apache2' ? 'Apache' : server.web_server}}
                        </span>
                    </div>
                    <div class="flex justify-between items-center gap-5 px-5 py-5">
                        <div class="w-16">
                            <div class="flex items-center gap-3">
                                <img
                                    v-if="server.database == 'mysql'"
                                    src="/svg/databases/mysql.svg"
                                    class="h-auto"
                                />
                                <img
                                    v-else
                                    src="/svg/databases/mariadb.svg"
                                    class="h-auto block"
                                />
                                <span>Database</span>
                            </div>
                        </div>
                        <span class="text-tiny text-gray-500 capitalize">{{ server.database }}</span>
                    </div>
                </template>
                <template v-else>
                    <div class="px-5 py-3 border-b last:border-b-0 border-slate-200" v-for="i in 4" :key="i">
                        <Skeleton :count="1"/>
                    </div>
                </template>
            </div>
            <div class="grid xl:grid-cols-2 md:grid-cols-2 grid-cols-1 gap-5">
                <div :class="[isLightColor ? 'border-custom-600' : 'border-custom-500', 'bg-white rounded-lg px-5 py-3 border-t-4']">
                    <ServerStats v-if="serverStats" statIcon="memory" :content="serverStats.serverInfo.processor" title="CPU Model Name"/>
                    <Skeleton :count="3" v-else/>
                </div>
                <div :class="[isLightColor ? 'border-custom-600' : 'border-custom-500', 'bg-white rounded-lg px-5 py-3 border-t-4']">
                    <ServerStats v-if="serverStats" statIcon="memory" :content="serverStats.cores" title="Cores"/>
                    <Skeleton :count="3" v-else/>
                </div>
                <div :class="[isLightColor ? 'border-custom-600' : 'border-custom-500', 'bg-white rounded-lg px-5 py-3 border-t-4']">
                    <ServerStats v-if="serverStats" statIcon="public" :content="serverStats.serverInfo.timezone" title="Timezone"/>
                    <Skeleton :count="3" v-else/>
                </div>
                <div :class="[isLightColor ? 'border-custom-600' : 'border-custom-500', 'bg-white rounded-lg px-5 py-3 border-t-4']">
                    <ServerStats v-if="serverStats" statIcon="update" :content="serverStats.serverUptime" title="Uptime" :customClass="serverStats.serverUptime.includes('year') ? ['!text-red-500'] : ['text-gray-500']"/>
                    <Skeleton :count="3" v-else/>
                </div>
            </div>
        </div>
        <div class="grid xl:grid-cols-4 sm:grid-cols-2 gap-5 mt-5">
            <div class="bg-white rounded-lg px-4 py-3" >
                <template v-if="serverStats !== null">
                    <div class="flex justify-between items-center gap-3 text-tiny">
                        <span class="text-base">Server Load</span>
                        <span :class="[isLightColor ? 'text-custom-600' : 'text-custom-500' ,'material-symbols-outlined text-2xl']">
                            dns
                        </span>
                    </div>
                    <div class="flex justify-center items-center">
                        <ServerLoadDonut :serverLoad="serverStats.serverLoad" :bg="progressBarColor(serverStats.serverLoad)" :color="remainingProgressBarColor(serverStats.serverLoad)"/>
                    </div>
                    <div class="flex justify-between items-center mt-1">
                        <div class="flex flex-col gap-1 text-tiny">
                            <span>Cores</span>
                            <span class="text-gray-500">{{ serverStats.cores }}</span>
                        </div>
                        <div class="flex flex-col gap-1 text-tiny">
                            <span>Average Load</span>
                            <span class="text-gray-500">15 Min</span>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <Skeleton :count="7"/>
                </template>
            </div>
            <div class="bg-white rounded-lg px-4 py-3">
                <template v-if="serverStats !== null">
                    <div class="flex justify-between items-center gap-3 text-tiny">
                        <span class="text-base">Memory Usage</span>
                        <span :class="[isLightColor ? 'text-custom-600' : 'text-custom-500' ,'material-symbols-outlined text-3xl']">
                            memory
                        </span>
                    </div>
                    <div class="flex justify-center items-center">
                        <MemoryUsageDonut :memoryUsage="memoryUsageInPercentage" :bg="progressBarColor(memoryUsageInPercentage)" :color="remainingProgressBarColor(memoryUsageInPercentage)"/>
                    </div>
                    <div class="flex justify-between items-center mt-1">
                        <div class="flex flex-col gap-1 text-tiny">
                            <span>Used</span>
                            <span class="text-gray-500">
                                {{ (serverStats.memory.total - serverStats.memory.available).toFixed(2) }}GB
                            </span>
                        </div>
                        <div class="flex flex-col gap-1 text-tiny">
                            <span>Available</span>
                            <span class="text-gray-500">
                                {{ serverStats.memory.available }}GB
                            </span>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <Skeleton :count="7"/>
                </template>
            </div>
            <div class="bg-white rounded-lg px-4 py-3">
                <template v-if="serverStats !== null">
                    <div class="flex justify-between items-center gap-3 text-tiny">
                        <span class="text-base">Disk Usage</span>
                        <span :class="[isLightColor ? 'text-custom-600' : 'text-custom-500' ,'material-symbols-outlined text-2xl']">
                            save
                        </span>
                    </div>
                    <div class="flex justify-center items-center">
                        <DiskUsageDonut :diskUsage="serverStats.disk.usage_in_percentage" :bg="progressBarColor(serverStats.disk.usage_in_percentage)" :color="remainingProgressBarColor(serverStats.disk.usage_in_percentage)"/>
                    </div>
                    <div class="flex justify-between items-center mt-1">
                        <div class="flex flex-col gap-1 text-tiny">
                            <span>Used</span>
                            <span class="text-gray-500">
                                {{ serverStats.disk.usage }}GB
                            </span>
                        </div>
                        <div class="flex flex-col gap-1 text-tiny">
                            <span>Available</span>
                            <span class="text-gray-500">
                                {{ (serverStats.disk.total - serverStats.disk.usage).toFixed(2) }}GB
                            </span>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <Skeleton :count="7"/>
                </template>
            </div>
            <div class="bg-white rounded-lg px-4 py-3">
                <template v-if="serverStats !== null">
                    <div class="flex justify-between items-center gap-3 text-tiny">
                        <span class="text-base">Swap Memory</span>
                        <span :class="[isLightColor ? 'text-custom-600' : 'text-custom-500' ,'material-symbols-outlined text-3xl']">
                            memory
                        </span>
                    </div>
                    <div class="flex justify-center items-center">
                        <SwapMemoryDonut :swapMemory="swapMemoryPercentage" :bg="progressBarColor(swapMemoryPercentage)" :color="remainingProgressBarColor(swapMemoryPercentage)"/>
                    </div>
                    <div class="flex justify-between items-center mt-1">
                        <div class="flex flex-col gap-1 text-tiny">
                            <span>Used</span>
                            <span class="text-gray-500">{{ (serverStats.swapMemory.total - serverStats.swapMemory.available).toFixed(2) }}GB</span>
                        </div>
                        <div class="flex flex-col gap-1 text-tiny">
                            <span>Available</span>
                            <span class="text-gray-500">{{ serverStats.swapMemory.available }}GB</span>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <Skeleton :count="7"/>
                </template>
            </div>
        </div>
        <div class="mt-6">
            <SectionHeader title="Top Request Insights"/>
            <RequestInsights :requestInsights="requestInsights"/>
        </div>
        <SectionHeader title="Resource Utilization" class="mt-6"/>
        <div class="bg-white rounded-lg px-5 py-3 mt-5">
            <h1 class="text-xl text-gray-900">Server Load</h1>
            <ServerLoad :chartData="serverLoad" v-if="serverLoad"/>
            <Skeleton :count="10" v-else/>
        </div>
        <div class="bg-white rounded-lg px-5 py-3 mt-5">
            <h1 class="text-xl text-gray-900">Resource Usage</h1>
            <ServerUsage :chartData="serverUsage" v-if="serverUsage"/>
            <Skeleton :count="10" v-else/>
        </div>
        <SectionHeader title="Bandwidth Usage Breakdown" class="mt-6"/>
        <div class="bg-white rounded-lg px-4 py-3 mt-5">
            <span class="text-lg px-2 py-1">
                Bandwidth Usage Trends
            </span>
            <BandwidthLineChart :chartData="bandwidth.chartData" v-if="bandwidth.chartData && !bandwidth.chartRefreshing && !loading"/>
            <Skeleton v-else :count="10"/>
        </div>
        <div class="mt-5">
            <!--  Top 5 Bandwidth Consume URL with Mime Type & Bandwidth Usage -->
            <HighBandwidthUrl :bandwidthConsumerUrl="bandwidthConsumerUrl" @change="setApi(); fetchBandwidthConsumerUrl(api.bandwidth_cunsume_url)"/>
        </div>
        <div class="mt-5">
            <IpWithBandwidth :ipWithBandwidth="ipWithBandwidth" @changePage="fetchIpWithBandwidth"/>
        </div>
        <div class="grid xl:grid-cols-2 grid-cols-1 gap-5 mt-5">
            <Table :head="bandwidth.thead" :bodyHeight="'max-h-[310px] min-h-[100px]'">
                <template #header>
                    <div class="bg-violet-950 bg-opacity-10 py-2 px-4 border-b border-zinc-300">
                        <span class="text-lg flex items-center gap-2">
                            Application Bandwidth Usage
                            <span class="material-symbols-outlined text-base mt-0.5 cursor-default" v-tooltip.right="'See how much data the application on your server is using, helping you identify potential bottlenecks or areas for optimization.'">
                                info
                            </span>
                        </span>
                    </div>
                </template>
                <template v-if="bandwidth.data.length && !bandwidth.refreshing && !loading">
                    <tr class="even:bg-slate-50 last:border-0 border-b border-gray-300" v-for="(data, key) in bandwidth.data" :key="key">
                        <td class="px-4 py-3.5 text-sm text-left">
                            {{ data.application_name }}
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
                    <template v-if="bandwidth.refreshing || loading">
                        <tr v-for="i in 5" :key="i">
                            <td class="whitespace-nowrap py-2 px-4 text-sm" v-for="j in 3" :key="j">
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
                    <div v-if="bandwidth.data.length && !bandwidth.refreshing && !loading">
                        <PerPage v-model="bandwidth.per_page" @change="fetchBandwidthWithApps"/>
                    </div>
                </template>
            </Table>
            
            <!--  File Type Usage Breakdown -->
            <Table :head="bandwidthFileType.thead" :bodyHeight="'max-h-[310px] min-h-[100px]'">
                <template #header>
                    <div class="bg-violet-950 bg-opacity-10 py-2 px-4 border-b border-zinc-300">
                        <span class="text-lg flex items-center gap-2">
                            File Type Usage Breakdown
                            <span class="material-symbols-outlined text-base mt-0.5 cursor-default" v-tooltip.right="'See how much bandwidth each file type is using within your specific application to identify potential storage needs or optimization opportunities.'">
                                info
                            </span>
                        </span>
                    </div>
                </template>
                <template v-if="bandwidthFileType.data.length && !bandwidthFileType.refreshing && !loading">
                    <tr class="even:bg-slate-50 last:border-0 border-b border-gray-300" v-for="(data, key) in bandwidthFileType.data" :key="key">
                        <td class="px-4 py-3.5 text-sm text-left">
                            {{ data.application_name }}
                        </td>
                        <td class="px-4 py-3.5 text-sm text-left">
                            {{ data.document_type }}
                        </td>
                        <td class="px-4 py-3.5 text-sm text-left">
                            {{ data.total_bandwidth_MB }} MB
                        </td>
                    </tr>
                </template>
                <template v-else>
                    <template v-if="bandwidthFileType.refreshing || loading">
                        <tr v-for="i in 5" :key="i">
                            <td class="whitespace-nowrap py-2 px-4 text-sm" v-for="j in 3" :key="j">
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
                    <div v-if="bandwidthFileType.data.length && !bandwidthFileType.refreshing && !loading">
                        <PerPage v-model="bandwidthFileType.per_page" @change="fetchBandwidthFileType"/>
                    </div>
                </template>
            </Table>
        </div>
        <div class="mt-5">
            <!-- Referrers (Referrer with Bandwidth Usage) -->
            <ReferrerBandwidth :referrerWithBandwidth="referrerWithBandwidth" @changePage="fetchReferrerWithBandwidth"/>
        </div>
        <SectionHeader title="IP Traffic Insights" class="mt-6"/>
        <div class="bg-white rounded-lg px-4 py-3 mt-5">
            <span class="text-lg">
                IP Visitors & Hits
            </span>
            <VisitorsAndHitsChart :chartData="ipChart.data" v-if="ipChart.data && !ipChart.refreshing && !loading" />
            <Skeleton :count="10" v-else/>
        </div>
        <div class="grid xl:grid-cols-2 grid-cols-1 gap-5 mt-5">
            <!-- IP Count By Application -->
            <IpCount :uniqueIp="uniqueIp" @changePage="fetchUniqueIp"/>

            <div class="bg-white rounded-lg px-4 py-3">
                <span class="text-lg">
                    Visitors IP
                </span>
                <VisitorsIPChart :hits="visitorsIpChart.data" v-if="visitorsIpChart.data && !visitorsIpChart.refreshing && !loading" />
                <Skeleton :count="10" v-else/>
            </div>
        </div>
        <SectionHeader title="HTTP Status Breakdown" class="mt-6"/>
        <StatusCodeCount :requestCount="requestCount"/>
        <div class="bg-white rounded-lg px-4 py-3 mt-5">
            <span class="text-lg flex items-center gap-2">
                Status Code Trends
                <span class="material-symbols-outlined text-base mt-0.5 cursor-default" v-tooltip.right="'Visualize how different types of responses are trending over time, helping you identify potential issues or areas for improvement.'">
                    info
                </span>
            </span>
            <StatusLineChart :chartData="statusCodes.lineChart" v-if="statusCodes.lineChart && !statusCodes.lineChartRefreshing && !loading"/>
            <Skeleton :count="10" v-else/>
        </div>
        <div class="grid xl:grid-cols-2 grid-cols-1 gap-5 mt-5">
            <div class="bg-white rounded-lg px-4 py-5">
                <span class="text-lg flex items-center gap-2">
                    Status Code Distribution
                    <span class="material-symbols-outlined text-base mt-0.5 cursor-default" v-tooltip.right="'See how different HTTP status codes are proportioned among server/application responses, revealing potential areas for optimization or troubleshooting.'">
                        info
                    </span>
                </span>
                <StatusPieChart :chartData="statusCodes.pieChart.data" v-if="statusCodes.pieChart.data && !statusCodes.pieChart.refreshing && !loading"/>
                <Skeleton v-else :count="10"/>
            </div>

            <!-- Status Codes (Top URL With Status) -->
            <StatusWithUrl :statusCodes="statusCodes.statusWithUrl" @changePage="fetchStatusWithUrl"/>
        </div>
        <SectionHeader title="Referral Insights" class="mt-6"/>
        <div class="grid xl:grid-cols-2 grid-cols-1 gap-5 mt-5">
            <!-- Application Traffic Referrals -->
            <ReferrerHits :referrerWithApplication="referrerWithApplication" @changePage="fetchReferrerWithApplication"/>
            
            <div class="bg-white rounded-lg px-4 py-3">
                <span class="text-lg">
                    Referral Trends
                </span>
                <ReferrerLineChart :chartData="referrer.chartData" v-if="referrer.chartData && !referrer.chartRefreshing && !loading"/>
                <Skeleton v-else :count="10"/>
            </div>
        </div>
        <SectionHeader title="Request Analysis" class="mt-6"/>
        <div class="grid xl:grid-cols-2 grid-cols-1 gap-5 mt-5">
            <div class="bg-white rounded-lg px-4 py-3">
                <span class="text-lg flex items-center gap-2">
                    Method Distribution
                    <span class="material-symbols-outlined text-base mt-0.5 cursor-default" v-tooltip.right="'Visualize how different HTTP methods (GET, POST, PUT, etc.) are being used to access your specific application.'">
                        info
                    </span>
                </span>
                <MethodsPieChart :chartData="methods.chart" v-if="methods.chart && !methods.chartRefreshing && !loading"/>
                <Skeleton v-else :count="10"/>
            </div>

            <!-- Methods (Method Count By Application) -->
            <MethodCount :methods="methods" @changePage="fetchMethodCountByApplication"/>
        </div>
        <SectionHeader title="Endpoint Activity" class="mt-6"/>
        <div class="grid xl:grid-cols-2 grid-cols-1 gap-5 mt-5">
            <!-- Most Visited Pages -->
            <MostVisitedPages :mostVisitedPages="mostVisitedPages" @changePage="setApi(); fetchMostVisitedPages(api.most_visited_pages)"/>

            <!-- URL (Endpoint) Top URL accessed by IP address -->
            <UrlWithIp :url="url.urlWithIp" @changePage="fetchUrlWithIp"/>
        </div>
        <div class="mt-5">
            <!-- Endpoint Activity Summary -->
            <UrlWithMethodStatus :url="url.urlWithMethodStatus" @changePage="fetchUrlWithMethodStatus"/>
        </div>
        <SectionHeader title="User Agents & Bots" class="mt-6"/>
        <div class="grid xl:grid-cols-2 grid-cols-1 gap-5 mt-5">
            <BrowserWithUrl :url="url.urlWithBrowser" @changePage="fetchUrlWithBrowser"/>
            
            <div class="bg-white rounded-lg px-4 py-3">
                <span class="text-lg flex items-center gap-2">
                    Popular Browsers
                    <span class="material-symbols-outlined text-base mt-0.5 cursor-default" v-tooltip.right="'See which browsers are most frequently used to access your website, helping you understand your audience and optimize compatibility.'">
                        info
                    </span>
                </span>
                <BrowserPieChart :chartData="browser.chartData" v-if="browser.chartData && !browser.chartRefreshing && !loading"/>
                <Skeleton v-else :count="10"/>
            </div>
        </div>
        <div class="grid xl:grid-cols-2 grid-cols-1 gap-5 mt-5">
            <div class="bg-white rounded-lg px-4 py-5">
                <span class="text-lg flex items-center gap-2">
                    Bot Traffic Distribution
                    <span class="material-symbols-outlined text-base mt-0.5 cursor-default" v-tooltip.right="'Understand which bots are visiting your website, including search engines, crawlers, and other automated tools.'">
                        info
                    </span>
                </span>
                <BotsPieChart :chartData="bots.chartData" v-if="bots.chartData && !bots.chartRefreshing && !loading"/>
                <Skeleton v-else :count="10"/>
            </div>

            <!-- Bots (Bots With URL) -->
            <BotsWithUrl :bots="bots.botsWithUrl" @changePage="fetchBotsWithUrl"/>
        </div>
    </div>
</template>

<script>
import { defineAsyncComponent } from 'vue'
import apiMixin from '@/mixins/commonApi'
import MoniterLogMixin from '@/mixins/monitorLog'

export default{
    mixins: [apiMixin, MoniterLogMixin],
    data(){
        return{
            api: {},
            serverStats: null,
            requestInsights: null,
            serverLoad: null,
            serverUsage: null,
            bandwidthFileType: {
                data: [],
                per_page: '5',
                refreshing: false,
                thead: ['Application', 'Type', 'Bandwidth Usage'],
            },
            bandwidthConsumerUrl: {
                data: [],
                per_page: '5',
                thead: ['Application', 'URL', 'Mime Type', 'Bandwidth Usage'],
                refreshing: false
            },
            uniqueIp:{
                data: [],
                pagination: null,
                per_page: '5',
                refreshing: false,
                thead: ['Application', 'IP Address', 'Total']
            },
            visitorsIpChart:{
                data: null,
                refreshing: false
            },
            ipChart:{
                data: null,
                refreshing: false
            },
            statusCodes: {
                lineChart: null,
                lineChartRefreshing: false,
                statusWithUrl:{
                    data: [],
                    per_page: '5',
                    thead: ['Application', 'URL', 'Status', 'Hits'],
                    refreshing: false
                },
                pieChart:{
                    data: null,
                    refreshing: false
                }
            },
            requestCount: {
                success: 0,
                failed: 0,
                notFound: 0,
                redirection: 0,
                information: 0
            },
            methods:{
                data: [],
                refreshing: false,
                per_page: '5',
                thead: ['Application', 'Method', 'Hits'],
                chart: null,
                chartRefreshing: false
            },
            referrerWithApplication:{
                data: [],
                refreshing: false,
                per_page: '5',
                thead: ['Application', 'Referrer', 'Hits']
            },
            referrerWithBandwidth:{
                data: [],
                refreshing: false,
                per_page: '5',
                thead: ['Application', 'Referrer', 'Bandwidth Usage', 'Hits']
            },
            url:{
                urlWithIp:{
                    data: [],
                    refreshing: false,
                    per_page: '5',
                    thead: ['Application', 'Endpoint', 'IP Address', 'Total'],
                },
                urlWithMethodStatus:{
                    data: [],
                    refreshing: false,
                    per_page: '5',
                    thead: ['Application', 'Endpoint', 'Method', 'Status', 'Hits']
                },
                urlWithBrowser:{
                    data: [],
                    refreshing: false,
                    per_page: '5',
                    thead: ['Application', 'Browser', 'URL', 'Hits']
                }
            },
            bots:{
                chartData: null,
                chartRefreshing: false,
                botsWithUrl: {
                    data: [],
                    refreshing: false,
                    per_page: '5',
                    thead: ['Application', 'Bot Name', 'URL', 'Hits']
                }
            },
            ipWithBandwidth:{
                data: [],
                refreshing: false,
                per_page: '5',
                thead: ['Application', 'IP Address', 'Bandwidth Usage', 'Hits', 'Last Visit']
            },
            bandwidth:{
                chartData: null,
                chartRefreshing: false,
                data: [],
                refreshing: false,
                per_page: '5',
                thead: ['Application', 'Bandwidth Usage', 'Hits']
            },
            browser:{
                chartData: null,
                chartRefreshing: false,
                totalCount: 0
            },
            mostVisitedPages:{
                data: [],
                refreshing: false,
                per_page: '5',
                thead: ['Application', 'Title', 'URL', 'Hits'],
            },
            referrer:{
                chartData: null,
                chartRefreshing: false
            }
        }
    },
    components: {
        ServerLoadDonut: defineAsyncComponent(() => import('@/components/charts/serverDashboard/ServerLoadDonut.vue')),
        MemoryUsageDonut: defineAsyncComponent(() => import('@/components/charts/serverDashboard/MemoryUsageDonut.vue')),
        DiskUsageDonut: defineAsyncComponent(() => import('@/components/charts/serverDashboard/DiskUsageDonut.vue')),
        SwapMemoryDonut: defineAsyncComponent(() => import('@/components/charts/serverDashboard/SwapMemoryDonut.vue')),
        ServerLoad: defineAsyncComponent(() => import('@/components/charts/serverDashboard/ServerLoadAverage.vue')),
        ServerUsage: defineAsyncComponent(() => import('@/components/charts/serverDashboard/ServerUsage.vue')),
        VisitorsIPChart: defineAsyncComponent(() => import('@/components/charts/common/VisitorsIp.vue')),
        VisitorsAndHitsChart: defineAsyncComponent(() => import('@/components/charts/common/Visitors&Hits.vue')),
        StatusLineChart: defineAsyncComponent(() => import('@/components/charts/common/StatusLine.vue')),
        StatusPieChart: defineAsyncComponent(() => import('@/components/charts/common/StatusPie.vue')),
        MethodsPieChart: defineAsyncComponent(() => import('@/components/charts/common/Methods.vue')),
        BotsPieChart: defineAsyncComponent(() => import('@/components/charts/common/BotsPie.vue')),
        BandwidthLineChart: defineAsyncComponent(() => import('@/components/charts/common/BandwidthLine.vue')),
        ReferrerLineChart: defineAsyncComponent(() => import('@/components/charts/common/ReferrerLine.vue')),
        BrowserPieChart: defineAsyncComponent(() => import('@/components/charts/common/BrowserPie.vue')),
        RequestInsights: defineAsyncComponent(() => import('@/components/user/RequestInsights.vue')),
        HighBandwidthUrl: defineAsyncComponent(() => import('@/components/user/HighBandwidthUrl.vue')),
        IpCount: defineAsyncComponent(() => import('@/components/user/IpCount.vue')),
        MethodCount: defineAsyncComponent(() => import('@/components/user/MethodCount.vue')),
        StatusCodeCount: defineAsyncComponent(() => import('@/components/user/StatusCodeCount.vue')),
        StatusWithUrl: defineAsyncComponent(() => import('@/components/user/StatusWithUrl.vue')),
        ReferrerBandwidth: defineAsyncComponent(() => import('@/components/user/ReferrerBandwidth.vue')),
        IpWithBandwidth: defineAsyncComponent(() => import('@/components/user/IpWithBandwidth.vue')),
        BrowserWithUrl: defineAsyncComponent(() => import('@/components/user/BrowserWithUrl.vue')),
        BotsWithUrl: defineAsyncComponent(() => import('@/components/user/BotsWithUrl.vue')),
        UrlWithIp: defineAsyncComponent(() => import('@/components/user/UrlWithIp.vue')),
        MostVisitedPages: defineAsyncComponent(() => import('@/components/user/MostVisitedPages.vue')),
        ReferrerHits: defineAsyncComponent(() => import('@/components/user/ReferrerHits.vue')),
        UrlWithMethodStatus: defineAsyncComponent(() => import('@/components/user/UrlWithMethodStatus.vue')),
        ServerStats: defineAsyncComponent(() => import('@/components/user/ServerStats.vue'))
    },
    computed: {
        idAndFilterQueryParams(){
            return `server_id=${this.$route.params.server}&${this.queryParam}`
        },
        idAndDateParams(){
            return `server_id=${this.$route.params.server}&${this.dateQuery}`
        },
        memoryUsageInPercentage(){
            if(this.serverStats !== null){
                var memoryUsed = this.serverStats.memory.total - this.serverStats.memory.available
                return ((memoryUsed*100)/this.serverStats.memory.total).toFixed(1)
            }else{
                return 0
            }
        },
        swapMemoryPercentage(){
            if(this.serverStats !== null){
                var SwapMemory = this.serverStats.swapMemory.total - this.serverStats.swapMemory.available
                return ((SwapMemory*100)/this.serverStats.swapMemory.total).toFixed(1)
            }else{
                return 0
            }
        }
    },
    methods: {
        setApi(){
            return new Promise((resolve) => {
                this.api = {
                    bandwidth_cunsume_url: `/bandwidths/top-urls?per_page=${this.bandwidthConsumerUrl.per_page}&${this.idAndFilterQueryParams}`,
                    most_visited_pages: `/urls?per_page=${this.mostVisitedPages.per_page}&${this.idAndFilterQueryParams}`
                }
                resolve();
            })      
        },
        fetchData(){
            this.fetchServerDetails(),
            this.fetchServerStats(),
            this.fetchServerLoad(),
            this.fetchServerUsage(),
            this.fetchBandwidthFileType(),
            this.fetchBandwidthConsumerUrl(this.api.bandwidth_cunsume_url),
            this.fetchUniqueIp(),
            this.fetchVisitorsIpChart(),
            this.fetchIpLineChart(),
            this.fetchStatusLineChart(),
            this.fetchStatusPieChart(),
            this.fetchStatusWithUrl(),
            this.fetchMethodCountByApplication(),
            this.fetchMethodPieChart(),
            this.fetchReferrerWithApplication(),
            this.fetchReferrerWithBandwidth(),
            this.fetchUrlWithIp(),
            this.fetchUrlWithMethodStatus(),
            this.fetchUrlWithBrowser(),
            this.fetchBotsPieChart(),
            this.fetchBotsWithUrl(),
            this.fetchIpWithBandwidth(),
            this.fetchBandwidthLineChart(),
            this.fetchBandwidthWithApps(),
            this.fetchBrowserPieChart(),
            this.fetchRequestCount()
            this.fetchRequestInsights(`/top-insights?${this.idAndFilterQueryParams}`),
            this.fetchMostVisitedPages(this.api.most_visited_pages),
            this.fetchReferrerChart()
        },
        async fetchServerStats(){
            await this.$axios.get(`/servers/${this.$route.params.server}/live-usage`).then(({data}) => {
                this.serverStats = data.usage
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            })
        },
        async fetchServerLoad(){
            await this.$axios.get(`/servers/${this.$route.params.server}/load-chart?${this.dateQuery}`).then(({data})=>{
                this.serverLoad = data
            }).catch(()=>{
                this.$toast.error('Failed to load the server load chart!')
            })
        },
        async fetchServerUsage(){
            await this.$axios.get(`/servers/${this.$route.params.server}/usage-chart?${this.dateQuery}`).then(({data}) => {
                this.serverUsage = data
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            })
        },
        async fetchBandwidthFileType(){
            this.bandwidthFileType.refreshing = true
            await this.$axios.get(`/bandwidths/top-app-document-types?per_page=${this.bandwidthFileType.per_page}&${this.idAndFilterQueryParams}`).then(({data}) => {
                this.bandwidthFileType.data = data.documentTypeStatistics
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.bandwidthFileType.refreshing = false
            })
        },
        async fetchBandwidthWithApps(){
            this.bandwidth.refreshing = true
            await this.$axios.get(`/bandwidths/application-statistics?per_page=${this.bandwidth.per_page}&${this.idAndFilterQueryParams}`).then(({data}) => {
                this.bandwidth.data = data.applicationStatistics
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.bandwidth.refreshing = false
            })
        }
    }
}
</script>
