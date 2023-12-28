import { mapState } from 'pinia';
import { useFilterStore } from '@/store/filter';

export default{
    data(){
        return{
            date: [],
            isBotRequest: '',
            server: null
        }
    },
    watch: {
        'dates'(val){
            this.date = val
        },
        queryParam: {
            async handler() {
                if(typeof this.setApi === 'function'){
                    await this.setApi();
                }
                await this.verifyApi(this.fetchData);
            }
        }
    },
    created(){
        this.date = this.dates
    },
    computed: {
        ...mapState(useFilterStore, ['dates']),
        startDate(){
            if(this.date.length){
                return new Date(this.date[0]).toISOString()
            }
        },
        endDate(){
            if(this.date.length){
                return new Date(this.date[1]).toISOString()
            }
        },
        dateQuery(){
            return `start_date=${this.startDate}&end_date=${this.endDate}`
        },
        queryParam(){
            return `bot=${this.isBotRequest}&${this.dateQuery}` 
        }
    },
    methods: {
        async fetchServerDetails(){
            await this.$axios.get(`/servers/${this.$route.params.server}`).then(({data}) => {
                this.server = data.server
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            })
        },
        async fetchRequestInsights(url){
            this.requestInsightsRefreshing = true
            await this.$axios.get(url).then(({data}) => {
                this.requestInsights = data
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.requestInsightsRefreshing = false
            })
        }, 
        async fetchBandwidthConsumerUrl(url){
            this.bandwidthConsumerUrl.refreshing = true
            await this.$axios.get(url).then(({data}) => {
                this.bandwidthConsumerUrl.data = data.topBandwidthUrls
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.bandwidthConsumerUrl.refreshing = false
            })
        },
        async fetchSitemapHits(url){
            this.sitemapHits.chartRefreshing = true
            await this.$axios.get(url).then(({data}) => {
                this.sitemapHits.data = data.urls
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.sitemapHits.chartRefreshing = false
            })
        },
        async fetchUniqueIp(){
            this.uniqueIp.refreshing = true
            let url = `/ips?per_page=${this.uniqueIp.per_page}&${this.idAndFilterQueryParams}`
            await this.$axios.get(url).then(({data}) => {
                this.uniqueIp.data = data.ips
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.uniqueIp.refreshing = false
            })
        },
        async fetchVisitorsIpChart(){
            this.visitorsIpChart.refreshing = true
            await this.$axios.get(`/ips/chart?${this.idAndFilterQueryParams}`).then(({data}) => {
                this.visitorsIpChart.data = data.ipData
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.visitorsIpChart.refreshing = false
            })
        },
        async fetchIpLineChart(){
            this.ipChart.refreshing = true
            await this.$axios.get(`/ips/line-chart?${this.idAndFilterQueryParams}`).then(({data}) => {
                this.ipChart.data = data.ipData
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.ipChart.refreshing = false
            })
        },
        async fetchStatusLineChart(){
            this.statusCodes.lineChartRefreshing = true
            await this.$axios.get(`/statuses/line-chart?${this.idAndFilterQueryParams}`).then(({data}) => {
                this.statusCodes.lineChart = data.datas
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.statusCodes.lineChartRefreshing = false
            })
        },
        async fetchStatusPieChart(){
            this.statusCodes.pieChart.refreshing = true
            await this.$axios.get(`/statuses/pie-chart?${this.idAndFilterQueryParams}`).then(({data}) => {
                this.statusCodes.pieChart.data = data
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.statusCodes.pieChart.refreshing = false
            })
        },
        async fetchMethodCountByApplication(){
            this.methods.refreshing = true
            await this.$axios.get(`/methods?per_page=${this.methods.per_page}&${this.idAndFilterQueryParams}`).then(({data}) => {
                this.methods.data = data.methods
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.methods.refreshing = false
            })
        },
        async fetchMethodPieChart(){
            this.methods.chartRefreshing = true
            await this.$axios.get(`/methods/pie-chart?${this.idAndFilterQueryParams}`).then(({data}) => {
                this.methods.chart = data
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.methods.chartRefreshing = false
            })
        },
        async fetchStatusWithUrl(){
            this.statusCodes.statusWithUrl.refreshing = true
            await this.$axios.get(`/url-combine-data/status?per_page=${this.statusCodes.statusWithUrl.per_page}&${this.idAndFilterQueryParams}`).then(({data}) => {
                this.statusCodes.statusWithUrl.data = data.urls
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.statusCodes.statusWithUrl.refreshing = false
            })
        },
        async fetchUrlWithIp(){
            this.url.urlWithIp.refreshing = true
            await this.$axios.get(`/ip-with-url?per_page=${this.url.urlWithIp.per_page}&${this.idAndFilterQueryParams}`).then(({data}) => {
                this.url.urlWithIp.data = data.ips
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.url.urlWithIp.refreshing = false
            })
        },
        async fetchBotsPieChart(){
            this.bots.chartRefreshing = true
            await this.$axios.get(`/bots/pie-chart?${this.idAndDateParams}&bot=1`).then(({data}) => {
                this.bots.chartData = data
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.bots.chartRefreshing = false
            })
        },
        async fetchBotsWithUrl(){
            this.bots.botsWithUrl.refreshing = true
            await this.$axios.get(`/url-combine-data/bot_name?per_page=${this.bots.botsWithUrl.per_page}&${this.idAndDateParams}&bot=1`).then(({data}) => {
                this.bots.botsWithUrl.data = data.urls
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.bots.botsWithUrl.refreshing = false
            })
        },
        async fetchUrlWithBrowser(){
            this.url.urlWithBrowser.refreshing = true
            await this.$axios.get(`/url-combine-data/browser?per_page=${this.url.urlWithBrowser.per_page}&${this.idAndDateParams}&bot=0`).then(({data}) => {
                this.url.urlWithBrowser.data = data.urls
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.url.urlWithBrowser.refreshing = false
            })
        },
        async fetchIpWithBandwidth(){
            this.ipWithBandwidth.refreshing = true
            await this.$axios.get(`/ip-with-bandwidth?per_page=${this.ipWithBandwidth.per_page}&${this.idAndFilterQueryParams}`).then(({data}) => {
                this.ipWithBandwidth.data = data.ips
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.ipWithBandwidth.refreshing = false
            })
        },
        async fetchBandwidthLineChart(){
            this.bandwidth.chartRefreshing = true
            await this.$axios.get(`/bandwidths/line-chart?${this.idAndFilterQueryParams}`).then(({data}) => {
                this.bandwidth.chartData = data.bandwidthData
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.bandwidth.chartRefreshing = false
            })
        },
        async fetchBrowserPieChart(){
            this.browser.chartRefreshing = true
            await this.$axios.get(`/browsers/pie-chart?${this.idAndDateParams}&bot=0`).then(({data}) => {
                this.browser.chartData = data
                this.browser.totalCount = data.length
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.browser.chartRefreshing = false
            })
        },
        async fetchReferrerWithBandwidth(){
            this.referrerWithBandwidth.refreshing = true
            await this.$axios.get(`/referrer-bandwidth-statistics?per_page=${this.referrerWithBandwidth.per_page}&${this.idAndFilterQueryParams}`).then(({data}) => {
                this.referrerWithBandwidth.data = data.referrersBandwidthStatistics
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.referrerWithBandwidth.refreshing = false
            })
        },
        async fetchRequestCount(){            
            this.requestCount = {
                success: 0,
                failed: 0,
                notFound: 0,
                redirection: 0,
                information: 0
            }
            await this.$axios.get(`/statuses/details?${this.idAndFilterQueryParams}`).then(({data}) => {
                data.datas.map(data => {
                    if(data.status_alias == 'Successful'){
                        this.requestCount.success = data.hits
                    }else if(data.status_alias == 'Server_error'){
                        this.requestCount.failed = data.hits
                    }else if(data.status_alias == 'Client_error'){
                        this.requestCount.notFound = data.hits
                    }else if(data.status_alias == 'Redirection'){
                        this.requestCount.redirection = data.hits
                    }else if(data.status_alias == 'Informational'){
                        this.requestCount.information = data.hits
                    }
                })
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            })
        },
        async fetchMostVisitedPages(url){
            this.mostVisitedPages.refreshing = true
            await this.$axios.get(url).then(({data}) => {
                this.mostVisitedPages.data = data.urls
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.mostVisitedPages.refreshing = false
            })
        },
        async fetchReferrerChart(){
            this.referrer.chartRefreshing = true
            await this.$axios.get(`/referrer-line-chart?${this.idAndFilterQueryParams}`).then(({data}) => {
                this.referrer.chartData = data.referrerData
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.referrer.chartRefreshing = false
            })
        },
        async fetchReferrerWithApplication(){
            this.referrerWithApplication.refreshing = true
            await this.$axios.get(`/application-referrer-statistics?per_page=${this.referrerWithApplication.per_page}&${this.idAndFilterQueryParams}`).then(({data}) => {
                this.referrerWithApplication.data = data.applicationStatistics
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.referrerWithApplication.refreshing = false
            })
        },
        async fetchUrlWithMethodStatus(){
            this.url.urlWithMethodStatus.refreshing = true
            await this.$axios.get(`/url-with-method-status?per_page=${this.url.urlWithMethodStatus.per_page}&${this.idAndFilterQueryParams}`).then(({data}) => {
                this.url.urlWithMethodStatus.data = data.urls
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.url.urlWithMethodStatus.refreshing = false
            })
        },
    }
}