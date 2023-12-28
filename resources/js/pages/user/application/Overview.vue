<template>
    <div class="container-fluid">
        <div class="flex flex-wrap gap-3 justify-between items-center">
            <h1 class="text-2xl text-gray-900">Application Dashboard</h1>
            <div class="flex sm:flex-nowrap flex-wrap gap-4 ">
                <BotSwitch v-model="isBotRequest" />
                <DateFilter />
            </div>
        </div>
        <SectionHeader title="Overview" class="mt-6"/>
        <ServerOverview :server="server"/>
        <ApplicationOverview :application="application" :overview="overview"/>
        <div class="mt-5">
            <SectionHeader title="Top Request Insights"/>
            <RequestInsights :requestInsights="requestInsights"/>
        </div>
        <SectionHeader title="Access Log Summary" class="mt-6"/>
        <div class="mt-5 bg-white rounded-lg px-4 py-3">
            <span class="text-lg">
                Access Log Trends
            </span>
            <AccessLogLineChart :chartData="logs.chartData" v-if="logs.chartData && !logs.chartRefreshing && !loading"/>
            <Skeleton v-else :count="10"/>
        </div>
        <div class="mt-5">
            <!-- Access Logs -->
            <Table :head="logs.thead" :bodyHeight="'max-h-[310px] min-h-[100px]'">
                <template #header>
                    <TableTitle :tableTitle="'Access Logs'"/>
                </template>
                <template v-if="logs.data.length && !logs.refreshing && !loading">
                    <tr class="even:bg-slate-50 last:border-0 border-b border-gray-300" v-for="(data, key) in logs.data" :key="key">
                        <td class="px-4 py-3.5 text-sm text-left max-w-[300px] overflow-auto truncate">
                            <span v-tooltip="data.ip">{{ data.ip }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-sm text-left">
                            {{ data.time }}
                        </td>
                        <td class="px-4 py-3.5 text-sm text-left">
                            {{ data.method }}
                        </td>
                        <td class="px-4 py-3.5 text-sm text-left max-w-[200px] overflow-auto truncate">
                            <span v-tooltip="data.url">{{ data.url }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-sm text-left">
                            {{ data.status }}
                        </td>
                        <td class="px-4 py-3.5 text-sm text-left">
                            {{ data.bytes }} MB
                        </td>
                        <td class="px-4 py-3.5 text-sm text-left max-w-[200px] overflow-auto truncate">
                            <span v-tooltip="data.referrer_url">{{ data.referrer_url }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-sm text-left">
                            {{ data.browser == '0' ? 'Other' : data.browser }}
                        </td>
                        <td class="px-4 py-3.5 text-sm text-left">
                            {{ data.bot_name == '0' ? 'Other' : data.bot_name }}
                        </td>
                    </tr>
                </template>
                <template v-else>
                    <template v-if="logs.refreshing || loading">
                        <tr v-for="i in 5" :key="i">
                            <td class="whitespace-nowrap py-2 px-4 text-sm" v-for="j in 9" :key="j">
                                <Skeleton :count="1"/>
                            </td>
                        </tr>
                    </template>
                    <tr v-else>
                        <td colspan="9" class="text-center text-sm px-6 py-4">
                            No Data Found
                        </td>
                    </tr>
                </template>
                <template #pagination>
                    <div v-if="logs.data.length && !logs.refreshing && !loading">
                        <PerPage v-model="logs.per_page" @change="fetchLogs()"/>
                    </div>
                </template>
            </Table>
        </div>
        <SectionHeader title="Error Analysis" class="mt-6"/>
        <div class="bg-white rounded-lg px-4 py-3 mt-5">
            <span class="text-lg flex items-center gap-2">
                Error Rate
                <span class="material-symbols-outlined text-base mt-0.5 cursor-default" v-tooltip.right="'See the percentage of requests that resulted in errors for the application, indicating potential issues that need attention.'">
                    info
                </span>
            </span>
            <ErrorRateLineChart :chartData="errorRates.chartData" v-if="errorRates.chartData && !errorRates.chartRefreshing && !loading"/>
            <Skeleton v-else :count="10"/>
        </div>
        <div class="mt-5">
            <!-- Error Summary -->
            <Table :head="error_summary.thead" :bodyHeight="'max-h-[330px] min-h-[100px]'">
                <template #header>
                    <TableTitle :tableTitle="'Error Summary'"/>
                </template>
                <template v-if="error_summary.data.length && !error_summary.refreshing && !loading">
                    <tr class="even:bg-slate-50 last:border-0 border-b border-gray-300" v-for="(data, key) in error_summary.data" :key="key">
                        <td class="px-4 py-3.5 text-sm text-left">
                            {{ data.status }}
                        </td>
                        <td class="px-4 py-3.5 text-sm text-left">
                            {{ data.total_count }}
                        </td>
                        <td class="px-4 py-3.5 text-sm text-left">
                            {{ data.percentage }}%
                        </td>
                        <td class="px-4 py-3.5 text-sm text-left max-w-[300px]">
                            {{ statusCodeContent[data.status] && statusCodeContent[data.status].description ? statusCodeContent[data.status].description : '-' }}
                        </td>
                        <td class="px-4 py-3.5 text-sm text-left max-w-[300px]">
                            {{ statusCodeContent[data.status] && statusCodeContent[data.status].action ? statusCodeContent[data.status].action : '-' }}
                        </td>
                    </tr>
                </template>
                <template v-else>
                    <template v-if="error_summary.refreshing || loading">
                        <tr v-for="i in 5" :key="i">
                            <td class="whitespace-nowrap py-2 px-4 text-sm" v-for="j in 5" :key="j">
                                <Skeleton :count="1"/>
                            </td>
                        </tr>
                    </template>
                    <tr v-else>
                        <td colspan="5" class="text-center text-sm px-6 py-4">
                            No Data Found
                        </td>
                    </tr>
                </template>
                <template #pagination>
                    <div v-if="error_summary.data.length && !error_summary.refreshing && !loading">
                        <PerPage v-model="error_summary.per_page" @change="fetchErrorSummary()"/>
                    </div>
                </template>
            </Table>
        </div>
        <div class="grid xl:grid-cols-2 grid-cols-1 gap-5 mt-5">
            <!--  Distribution of 4xx Errors Across Applications -->
            <Table :head="applications4xx.thead" :bodyHeight="'max-h-[310px] min-h-[100px]'">
                <template #header>
                    <div class="bg-violet-950 bg-opacity-10 py-2 px-4 border-b border-zinc-300">
                        <span class="text-lg">
                            Distribution of 4xx Errors
                        </span>
                    </div>
                </template>
                <template v-if="applications4xx.data.length && !applications4xx.refreshing && !loading">
                    <tr class="even:bg-slate-50 last:border-0 border-b border-gray-300" v-for="(data, key) in applications4xx.data" :key="key">
                        <td class="px-4 py-3.5 text-sm text-left max-w-[300px] overflow-auto truncate" v-tooltip="data.url">
                            {{ data.url }}
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
                    <template v-if="applications4xx.refreshing || loading">
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
                    <div v-if="applications4xx.data.length && !applications4xx.refreshing && !loading">
                        <PerPage v-model="applications4xx.per_page" @change="fetchApplicationErrors('4xx', 'applications4xx')"/>
                    </div>
                </template>
            </Table>

            <!--  Distribution of 5xx Errors Across Applications -->
            <Table :head="applications5xx.thead" :bodyHeight="'max-h-[310px] min-h-[100px]'">
                <template #header>
                    <div class="bg-violet-950 bg-opacity-10 py-2 px-4 border-b border-zinc-300">
                        <span class="text-lg">
                            Distribution of 5xx Errors
                        </span>
                    </div>
                </template>
                <template v-if="applications5xx.data.length && !applications5xx.refreshing && !loading">
                    <tr class="even:bg-slate-50 last:border-0 border-b border-gray-300" v-for="(data, key) in applications5xx.data" :key="key">
                        <td class="px-4 py-3.5 text-sm text-left max-w-[300px] overflow-auto truncate" v-tooltip="data.url">
                            {{ data.url }}
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
                    <template v-if="applications5xx.refreshing || loading">
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
                    <div v-if="applications5xx.data.length && !applications5xx.refreshing && !loading">
                        <PerPage v-model="applications5xx.per_page" @change="fetchApplicationErrors('5xx', 'applications5xx')"/>
                    </div>
                </template>
            </Table>
        </div>
        <SectionHeader title="Bandwidth Usage Breakdown" class="mt-6"/>
        <div class="bg-white rounded-lg px-4 py-3 mt-5">
            <span class="text-lg px-2 py-1">
                Bandwidth Usage Trends
            </span>
            <BandwidthLineChart :chartData="bandwidth.chartData" v-if="bandwidth.chartData && !bandwidth.chartRefreshing && !loading"/>
            <Skeleton v-else :count="10"/>
        </div>
        <div class="grid xl:grid-cols-3 grid-cols-1 gap-5 mt-5">
            <div class="xl:col-span-2 col-span-1">
                <!--  High Bandwidth Consume URL with Mime Type & Bandwidth Usage -->
                <HighBandwidthUrl :bandwidthConsumerUrl="bandwidthConsumerUrl" @change="setApi(); fetchBandwidthConsumerUrl(api.bandwidth_cunsume_url)"/>
            </div>

            <!--  Application Bandwidth Usage by File Type -->
            <FileTypeUsage :fileTypes="bandwidthFileType" @changePage="fetchBandwidthFileType"/>
        </div>
        <div class="grid xl:grid-cols-2 grid-cols-1 gap-5 mt-5">
            <IpWithBandwidth :ipWithBandwidth="ipWithBandwidth" @changePage="fetchIpWithBandwidth"/>

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
        <div class="mt-5">
            <StatusCodeCount :requestCount="requestCount"/>
        </div>
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
        <div class="bg-white rounded-lg px-4 py-3 mt-5">
            <span class="text-lg flex items-center gap-2">
                Throughput
                <span class="material-symbols-outlined text-base mt-0.5 cursor-default" v-tooltip.right="'Visualize how many requests your application is successfully processing each hour, helping you gauge performance and identify peak usage patterns'">
                    info
                </span>
            </span>
            <ThroughputLineChart :chartData="throughputs.chartData" v-if="throughputs.chartData && !throughputs.chartRefreshing && !loading"/>
            <Skeleton v-else :count="10"/>
        </div>
        <div class="grid xl:grid-cols-2 grid-cols-1 gap-5 mt-6">
            <!-- Methods (Method Count By Application) -->
            <MethodCount :methods="methods" @changePage="fetchMethodCountByApplication"/>

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
        </div>
        <SectionHeader title="Endpoint Activity" class="mt-6"/>
        <div class="mt-5">
            <!-- Endpoint Activity Summary -->
            <UrlWithMethodStatus :url="url.urlWithMethodStatus" @changePage="fetchUrlWithMethodStatus"/>
        </div>
        <div class="grid xl:grid-cols-2 grid-cols-1 gap-5 mt-5">
            <!-- Most Visited Pages -->
            <MostVisitedPages :mostVisitedPages="mostVisitedPages" @changePage="setApi(); fetchMostVisitedPages(api.most_visited_pages)"/>

            <!-- URL (Endpoint) Top URL accessed by IP address -->
            <UrlWithIp :url="url.urlWithIp" @changePage="fetchUrlWithIp"/>
        </div>
        <div class="mt-6">
            <!-- Sitemap URL Hits with User Agent, Method, Status & Timestamp -->
            <SitemapUrl :sitemapHits="sitemapHits" @changePage="setApi(); fetchSitemapHits(api.sitemap_hits)"/>
        </div>
        <div class="mt-6" v-if="application && application.framework == 'wordpress'">
            <!-- Xmlrpc URL Hits with User Agent, Method, Status & Timestamp -->
            <Table :head="xmlrpc.thead" :bodyHeight="'max-h-[310px] min-h-[100px]'">
                <template #header>
                    <TableTitle :tableTitle="'XMLRPC URL Activity'"/>
                </template>
                <template v-if="xmlrpc.data.length && !xmlrpc.refreshing && !loading">
                    <tr class="even:bg-slate-50 last:border-0 border-b border-gray-300" v-for="(data, key) in xmlrpc.data" :key="key">
                        <td class="px-4 py-3.5 text-sm text-left max-w-[300px] overflow-auto truncate" v-tooltip="data.url">
                            {{ data.url }}
                        </td>
                        <td class="px-4 py-3.5 text-sm text-left">
                            {{ data.method }}
                        </td>
                        <td class="px-4 py-3.5 text-sm text-left">
                            {{ data.status }}
                        </td>
                        <td class="px-4 py-3.5 text-sm text-left">
                            {{ data.browser == '0' ? 'Other' : data.browser }}
                        </td>
                        <td class="px-4 py-3.5 text-sm text-left">
                            {{ data.created_at }}
                        </td>
                    </tr>
                </template>
                <template v-else>
                    <template v-if="xmlrpc.refreshing || loading">
                        <tr v-for="i in 5" :key="i">
                            <td class="whitespace-nowrap py-2 px-4 text-sm" v-for="j in 5" :key="j">
                                <Skeleton :count="1"/>
                            </td>
                        </tr>
                    </template>
                    <tr v-else>
                        <td colspan="5" class="text-center text-sm px-6 py-4">
                            No Data Found
                        </td>
                    </tr>
                </template>
                <template #pagination>
                    <div v-if="xmlrpc.data.length && !xmlrpc.refreshing && !loading">
                        <PerPage v-model="xmlrpc.per_page" @change="fetchXmlrpcUrl()"/>
                    </div>
                </template>
            </Table>
        </div>
        <SectionHeader title="User Agent & Bots" class="mt-6"/>
        <div class="grid xl:grid-cols-2 grid-cols-1 gap-5 mt-6">
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

            <BrowserWithUrl :url="url.urlWithBrowser" @changePage="fetchUrlWithBrowser"/>
        </div>
        <div class="grid xl:grid-cols-2 grid-cols-1 gap-5 mt-6">
            <!-- Bots (Bots With URL) -->
            <BotsWithUrl :bots="bots.botsWithUrl" @changePage="fetchBotsWithUrl"/>
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
        </div>
    </div>
</template>

<script>
import { defineAsyncComponent } from 'vue'
import apiMixin from '@/mixins/commonApi'
import statusCodeData from '@/statusCodeContent'

export default{
    mixins: [apiMixin],
    data(){
        return{
            api:{},
            application: null,
            overview: null,
            requestInsights: null,
            statusCodeContent: null,
            bandwidthFileType: {
                data: [],
                per_page: '5',
                refreshing: false,
                thead: ['Type', 'Bandwidth Usage', 'Hits'],
            },
            bandwidthConsumerUrl: {
                data: [],
                per_page: '5',
                thead: ['URL', 'Mime Type', 'Bandwidth Usage'],
                refreshing: false
            },
            throughputs:{
                chartData: null,
                chartRefreshing: false
            },
            errorRates:{
                chartData: null,
                chartRefreshing: false
            },
            sitemapHits: {
                data: [],
                per_page: '5',
                thead: ['Sitemap URL', 'Bot Name', 'Method', 'Status', 'Timestamp'],
                refreshing: false
            },
            uniqueIp:{
                data: [],
                pagination: null,
                per_page: '5',
                refreshing: false,
                thead: ['IP Address', 'Total']
            },
            visitorsIpChart:{
                data: null,
                refreshing: false
            },
            ipChart:{
                data: null,
                refreshing: false
            },
            methods:{
                data: [],
                refreshing: false,
                per_page: '5',
                thead: ['Method', 'Hits'],
                chart: null,
                chartRefreshing: false
            },
            statusCodes: {
                lineChart: null,
                lineChartRefreshing: false,
                statusWithUrl:{
                    data: [],
                    per_page: '5',
                    thead: ['URL', 'Status', 'Hits'],
                    refreshing: false
                },
                pieChart:{
                    data: null,
                    refreshing: false
                }
            },
            applications4xx:{
                data: [],
                refreshing: false,
                per_page: '5',
                thead: ['URL', 'Request', 'Last Request At']
            },
            applications5xx:{
                data: [],
                refreshing: false,
                per_page: '5',
                thead: ['URL', 'Request', 'Last Request At']
            },
            requestCount: {
                success: 0,
                failed: 0,
                notFound: 0,
                redirection: 0,
                information: 0
            },
            referrerWithBandwidth:{
                data: [],
                refreshing: false,
                per_page: '5',
                thead: ['Referrer', 'Bandwidth Usage', 'Hits']
            },
            ipWithBandwidth:{
                data: [],
                refreshing: false,
                per_page: '5',
                thead: ['IP Address', 'Bandwidth', 'Hits', 'Last Visit']
            },
            bandwidth:{
                chartData: null,
                chartRefreshing: false,
            },
            url:{
                urlWithIp:{
                    data: [],
                    refreshing: false,
                    per_page: '5',
                    thead: ['Endpoint', 'IP Address', 'Hits'],
                },
                urlWithBrowser:{
                    data: [],
                    refreshing: false,
                    per_page: '5',
                    thead: ['Browser', 'URL', 'Hits']
                },
                urlWithMethodStatus:{
                    data: [],
                    refreshing: false,
                    per_page: '5',
                    thead: ['Endpoint', 'Method', 'Status', 'Hits']
                },
            },
            browser:{
                chartData: null,
                chartRefreshing: false,
                totalCount: 0
            },
            bots:{
                chartData: null,
                chartRefreshing: false,
                botsWithUrl: {
                    data: [],
                    refreshing: false,
                    per_page: '5',
                    thead: ['Bot Name', 'URL', 'Hits']
                }
            },
            xmlrpc:{
                data: [],
                per_page: '5',
                thead: ['URL', 'Method', 'Status', 'User-Agent', 'Timestamp'],
                refreshing: false
            },
            error_summary:{
                data: [],
                per_page: '5',
                refreshing: false,
                thead: ['Error Code', 'Count', 'Percentage', 'Description', 'Actionable Insight']
            },
            logs:{
                data: [],
                refreshing: false,
                per_page: '5',
                thead: ['IP Address', 'Time', 'Method', 'URL', 'Status', 'Bandwidth', 'Referrer', 'Browser', 'Bot Name'],
                chartData: null,
                chartRefreshing: false
            },
            mostVisitedPages:{
                data: [],
                refreshing: false,
                per_page: '5',
                thead: ['Title', 'URL', 'Hits'],
            },
            referrerWithApplication:{
                data: [],
                refreshing: false,
                per_page: '5',
                thead: ['Referrer', 'Hits']
            },
            referrer:{
                chartData: null,
                chartRefreshing: false
            }
        }
    },
    created(){
        this.statusCodeContent = statusCodeData
    },
    components:{
        ApplicationOverview: defineAsyncComponent(() => import('@/components/user/ApplicationOverview.vue')),
        StatusLineChart: defineAsyncComponent(() => import('@/components/charts/common/StatusLine.vue')),
        StatusPieChart: defineAsyncComponent(() => import('@/components/charts/common/StatusPie.vue')),
        ThroughputLineChart: defineAsyncComponent(() => import('@/components/charts/applicationDashboard/ThroughputLine.vue')),
        ErrorRateLineChart: defineAsyncComponent(() => import('@/components/charts/applicationDashboard/ErrorRateLine.vue')),
        VisitorsIPChart: defineAsyncComponent(() => import('@/components/charts/common/VisitorsIp.vue')),
        VisitorsAndHitsChart: defineAsyncComponent(() => import('@/components/charts/common/Visitors&Hits.vue')),
        MethodsPieChart: defineAsyncComponent(() => import('@/components/charts/common/Methods.vue')),
        BandwidthLineChart: defineAsyncComponent(() => import('@/components/charts/common/BandwidthLine.vue')),
        BrowserPieChart: defineAsyncComponent(() => import('@/components/charts/common/BrowserPie.vue')),
        BotsPieChart: defineAsyncComponent(() => import('@/components/charts/common/BotsPie.vue')),
        AccessLogLineChart: defineAsyncComponent(() => import('@/components/charts/applicationDashboard/AccessLogsLine.vue')),
        RequestInsights: defineAsyncComponent(() => import('@/components/user/RequestInsights.vue')),
        SitemapUrl: defineAsyncComponent(() => import('@/components/user/SitemapUrl.vue')),
        HighBandwidthUrl: defineAsyncComponent(() => import('@/components/user/HighBandwidthUrl.vue')),
        FileTypeUsage: defineAsyncComponent(() => import('@/components/user/FileTypeUsage.vue')),
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
        ReferrerLineChart: defineAsyncComponent(() => import('@/components/charts/common/ReferrerLine.vue')),
        ReferrerHits: defineAsyncComponent(() => import('@/components/user/ReferrerHits.vue')),
        UrlWithMethodStatus: defineAsyncComponent(() => import('@/components/user/UrlWithMethodStatus.vue'))
    },
    computed: {
        idAndFilterQueryParams(){
            return `server_id=${this.$route.params.server}&application_id=${this.$route.params.application}&${this.queryParam}`
        },
        idAndDateParams(){
            return `server_id=${this.$route.params.server}&application_id=${this.$route.params.application}&${this.dateQuery}`
        },
    },
    methods: {
        setApi(){
            return new Promise((resolve) => {
                this.api = {
                    bandwidth_cunsume_url: `/bandwidths/top-urls?per_page=${this.bandwidthConsumerUrl.per_page}&${this.idAndFilterQueryParams}`,
                    sitemap_hits: `specific-urls/is_sitemap_url?per_page=${this.sitemapHits.per_page}&${this.idAndFilterQueryParams}`,
                    most_visited_pages: `/urls?per_page=${this.mostVisitedPages.per_page}&${this.idAndFilterQueryParams}`
                }
                resolve();
            })      
        },
        fetchData(){
            this.fetchServerDetails(),
            this.fetchApplicationDetails(),
            this.fetchApplicationOverview(),
            this.fetchStatusLineChart(),
            this.fetchStatusPieChart(),
            this.fetchStatusWithUrl(),
            this.fetchBandwidthFileType(),
            this.fetchBandwidthConsumerUrl(this.api.bandwidth_cunsume_url),
            this.fetchThroughputChart(),
            this.fetchErrorRatesChart(),
            this.fetchSitemapHits(this.api.sitemap_hits),
            this.fetchUniqueIp(),
            this.fetchVisitorsIpChart(),
            this.fetchIpLineChart(),
            this.fetchMethodCountByApplication(),
            this.fetchMethodPieChart(),
            this.fetchReferrerWithBandwidth(),
            this.fetchIpWithBandwidth(),
            this.fetchBandwidthLineChart(),
            this.fetchUrlWithIp(),
            this.fetchUrlWithBrowser(),
            this.fetchBrowserPieChart(),
            this.fetchBotsPieChart(),
            this.fetchBotsWithUrl(),
            this.fetchRequestCount(),
            this.fetchApplicationErrors('4xx', 'applications4xx'),
            this.fetchApplicationErrors('5xx', 'applications5xx'),
            this.fetchRequestInsights(`/top-insights?${this.idAndFilterQueryParams}`)
            this.fetchXmlrpcUrl(),
            this.fetchLogs(),
            this.fetchLogChart(),
            this.fetchErrorSummary(),
            this.fetchMostVisitedPages(this.api.most_visited_pages),
            this.fetchReferrerWithApplication(),
            this.fetchReferrerChart(),
            this.fetchUrlWithMethodStatus()
        },
        async fetchApplicationDetails(){
            await this.$axios.get(`/servers/${this.$route.params.server}/applications/${this.$route.params.application}`).then(({data}) => {
                this.application = data.application
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            })
        },
        async fetchApplicationOverview(){
            await this.$axios.get(`/servers/${this.$route.params.server}/applications/${this.$route.params.application}/overview?${this.queryParam}`).then(({data}) => {
                this.overview = data
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            })
        },
        async fetchLogs(){
            this.logs.refreshing = true
            await this.$axios.get(`servers/${this.$route.params.server}/applications/${this.$route.params.application}/access-logs?per_page=${this.logs.per_page}&${this.queryParam}`).then(({data}) => {
                this.logs.data = data.accessLogs
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.logs.refreshing = false
            })
        },
        async fetchLogChart(){
            this.logs.chartRefreshing = true
            await this.$axios.get(`servers/${this.$route.params.server}/applications/${this.$route.params.application}/access-logs-chart?${this.queryParam}`).then(({data}) => {
                this.logs.chartData = data.accessLogsData
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.logs.chartRefreshing = false
            })
        },
        async fetchBandwidthFileType(){
            this.bandwidthFileType.refreshing = true
            await this.$axios.get(`/bandwidths/top-document-types?per_page=${this.bandwidthFileType.per_page}&${this.idAndFilterQueryParams}`).then(({data}) => {
                this.bandwidthFileType.data = data.documentTypeStatistics
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.bandwidthFileType.refreshing = false
            })
        },
        async fetchThroughputChart(){
            this.throughputs.chartRefreshing = true
            await this.$axios.get(`/throughputs?${this.idAndFilterQueryParams}`).then(({data}) => {
                if(data.lineChartData.length){
                    this.throughputs.chartData = data.lineChartData[0]
                }else{
                    this.throughputs.chartData = {}
                }
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.throughputs.chartRefreshing = false
            })
        },
        async fetchErrorRatesChart(){
            this.errorRates.chartRefreshing = true
            await this.$axios.get(`/error-rates?${this.idAndFilterQueryParams}`).then(({data}) => {
                if(data.lineChartData.length){
                    this.errorRates.chartData = data.lineChartData[0]
                }else{
                    this.errorRates.chartData = {}
                }
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.errorRates.chartRefreshing = false
            })
        },
        async fetchApplicationErrors(type, dataProperty){
            this[dataProperty].refreshing = true
            await this.$axios.get(`/url-count-by-status/${type}?per_page=${this[dataProperty].per_page}&${this.idAndFilterQueryParams}`).then(({data}) => {
                this[dataProperty].data = data
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this[dataProperty].refreshing = false
            })
        },
        async fetchXmlrpcUrl(){
            this.xmlrpc.refreshing = true
            await this.$axios.get(`/specific-urls/is_xmlrpc_request?per_page=${this.xmlrpc.per_page}&${this.idAndFilterQueryParams}`).then(({data}) => {
                this.xmlrpc.data = data.urls
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.xmlrpc.refreshing = false
            })
        },
        async fetchErrorSummary(){
            this.error_summary.refreshing = true
            await this.$axios.get(`/statuses/pie-chart?error_summary=true&per_page=${this.error_summary.per_page}&${this.idAndFilterQueryParams}`).then(({data}) => {
                this.error_summary.data = data
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.error_summary.refreshing = false
            })
        },
    }
}
</script>
