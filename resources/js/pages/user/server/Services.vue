<template>
    <div class="container-fluid">
        <div class="flex flex-wrap gap-3 justify-between items-center">
            <h1 class="text-2xl text-gray-900">Services</h1>
            <div class="flex sm:flex-nowrap flex-wrap gap-4 w-[250px] max-w-xl">
                <DateFilter />
            </div>
        </div>
        <ServerOverview :server="server"/>
        <h1 class="text-lg text-gray-900 mt-5">Service Usage Overview</h1>
        <div class="grid xl:grid-cols-2 grid-cols-1 gap-5 mt-3" v-if="Object.keys(chartData).length">
            <template v-for="(value, key) in chartData" :key="key">
                <div class="bg-neutral-50 border border-neutral-200 rounded-lg px-5 py-3">
                    <div class="flex flex-wrap justify-between items-center gap-5">
                        <div class="flex items-center gap-3">
                            <span class="capitalize text-gray-800 font-cera-pro-bold">{{ key }}</span>
                            <img v-if="key=='nginx'" src="/svg/services/nginx.svg" class="w-8 inline"/>
                            <img v-else-if="key=='apache2'" src="/svg/services/apache.svg" class="w-6 inline"/>
                            <img v-else-if="key=='openlitespeed'" src="/svg/services/ols.svg" class="w-8 inline"/>
                            <img v-else-if="key=='mysql'" src="/svg/services/mysql.svg" class="w-16 inline"/>
                            <img v-else-if="key=='mariadb'" src="/svg/databases/mariadb1.svg" class="w-8 inline"/>
                            <img v-else-if="key=='postfix'" src="/svg/services/postfix.png" class="w-8 inline"/>
                            <img v-else-if="key=='redis'" src="/svg/services/redis.svg" class="w-6 inline"/>
                            <img v-else-if="key=='ssh'" src="/svg/services/ssh.svg" class="w-6 inline"/>
                            <img v-else-if="key=='ufw'" src="/svg/services/ufw.svg" class="w-6 inline"/>
                            <img v-else-if="key=='fail2ban'" src="/svg/services/fail2ban.svg" class="w-6 inline"/>
                            <img v-else-if="key=='php8.1-fpm'" src="/images/php_versions/8.1.png" class="w-8 inline"/>
                            <img v-else-if="key=='php8.2-fpm'" src="/images/php_versions/8.2.png" class="w-8 inline"/>
                            <img v-else-if="key=='php8.0-fpm'" src="/images/php_versions/php8.webp" class="w-11 inline"/>
                            <img v-else src="/images/php_versions/php.png" class="w-8 inline"/>
                        </div>
                        <span 
                            class="px-2 py-1.5 flex items-center gap-1.5 rounded-2xl text-xs"
                            :class="serviceStatus(key) ? 'text-green-500 bg-green-100' : 'text-red-500 bg-red-100'"
                        >
                            <i class="fas fa-circle"></i>
                            {{ serviceStatus(key) ? 'Running' : 'Stopped' }}
                        </span>
                    </div>
                    <div class="flex flex-wrap justify-between items-center gap-5 mt-3">
                        <div class="flex flex-col gap-1">
                            <span class="text-tiny">CPU Usage</span>
                            <span class="text-gray-500 font-light">
                                {{ cpuUsage(key) }}%
                            </span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-tiny">Memory Usage</span>
                            <span class="text-gray-500 font-light">
                                {{ memoryUsage(key) }}%
                            </span>
                        </div>
                    </div>
                    <div class="mt-5">
                        <ServiceChart :chartData="value"/>
                    </div>
                </div>
            </template>
        </div>
        <template v-if="refreshing || loading">
            <div class="grid xl:grid-cols-2 grid-cols-1 gap-5 mt-3">
                <div v-for="n in 4" :key="n" class="bg-neutral-50 border border-neutral-200 rounded-lg px-5 py-3">
                    <Skeleton :count="15"/>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
import apiMixin from '@/mixins/commonApi'
import { defineAsyncComponent } from 'vue';

export default{
    mixins: [apiMixin],
    data(){
        return {
            services: null,
            servicesNames: [],
            chartData: {},
            refreshing: false
        }
    },
    components: {
        ServiceChart: defineAsyncComponent(() => import('@/components/charts/services.vue'))
    },
    methods:{
        serviceStatus(service){
            let ser = this.services.find(data => data.name == service)
            return ser.status
        },
        cpuUsage(service){
            let ser = this.services.find(data => data.name == service)
            return ser.resourceUsage.cpu
        },
        memoryUsage(service){
            let ser = this.services.find(data => data.name == service)
            return ser.resourceUsage.ram
        },
        async fetchData() {
            await Promise.all([
                this.fetchServerDetails(),
                this.fetchServices(),
                this.fetchChartData()
            ])
        },
        async fetchServices(){
            this.refreshing = true
            await this.$axios.get(`/servers/${this.$route.params.server}/services`).then(({data}) => {
                this.services = data.services
                this.servicesNames = data.services.map(service => {
                    return service.name
                })
                this.fetchChartData()
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.refreshing = false
            })
        },
        async fetchChartData(){
            for (const service of this.servicesNames) {
                if(this.$route.params.server){
                    await this.serviceChart(service);
                }
            }
        },
        async serviceChart(service){
            await this.$axios.get(`/servers/${this.$route.params.server}/services/${service}/chart?${this.dateQuery}`).then(({data}) => {
                this.chartData[service] = data.services;
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            })
        }
    }
}
</script>
