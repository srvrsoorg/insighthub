<template>
    <div class="container-fluid">
        <h1 class="text-2xl text-black">Applications</h1>
        <div class="grid md:grid-cols-4 sm:grid-cols-2 grid-cols-1 gap-5 items-center mt-3">
            <select
                @change="fetchApplication()"
                class="w-full placeholder:font-italitc text-sm border-0 bg-white focus:ring-0 rounded-md py-2.5 focus:outline-none"
                v-model="currentServerId"
            >
                <option class="capitalize" v-for="server in servers" :key="server.id" :value="server.id">{{server.name}} ({{server.ip}})</option>
            </select>
            <label class="relative block text-[#8C8C8C]">
                <span
                    class="absolute inset-y-0 left-0 flex items-center pl-3"
                >
                    <i class="las la-search text-xl -rotate-90"></i>
                </span>
                <input
                    @input="debouncedList()"
                    v-model="query.search"
                    class="w-full h-full placeholder:font-italitc text-sm bg-white border-0 focus:ring-0 rounded-md py-3 pl-10 pr-4 focus:outline-none"
                    placeholder="Search"
                    type="text"
                />
            </label>
        </div>
        <Table class="mt-5" :head="thead" v-if="applications.length > 0">
            <tr class="even:bg-slate-50 last:border-0 border-b border-gray-300 " v-for="application in applications" :key="application.id">
                <td class="whitespace-nowrap py-4 px-4 text-sm flex flex-col gap-1">
                    <span>{{ currentServer.name }}</span>
                    <span>({{ currentServer.ip }})</span>
                </td>
                <td class="whitespace-nowrap py-4 px-4 text-sm">
                    {{ application.name }}
                </td>
                <td class="whitespace-nowrap py-4 px-4 text-sm">
                    {{ application.primary_domain }}
                    <a :href="'http://' + application.primary_domain" target="_blank" :class="[isLightColor ? 'text-custom-600' : 'text-custom-500']">
                        <i class="las la-external-link-alt"></i>
                    </a>
                </td>
                <td class="whitespace-nowrap py-4 px-4 text-sm capitalize">
                    {{ application.framework }}
                </td>
                <td class="whitespace-nowrap py-4 px-4 text-sm text-center">
                    <span v-if="application.php_version == 8">8.0</span>
                    <span v-else-if="application.php_version == 7">7.0</span>
                    <span v-else>{{ application.php_version }}</span>
                </td>
                <td class="whitespace-nowrap py-4 px-4 text-sm text-center">
                    <i class="fas fa-circle text-green-600" v-if="application.active"></i>
                    <i class="fas fa-circle text-red-500" v-else></i>
                </td>
                <td class="whitespace-nowrap py-4 px-4 text-sm text-center">
                    <span class="material-symbols-outlined text-lg text-green-600" v-tooltip="'SSL Installed'" v-if="application.ssl">lock</span>
                    <span class="material-symbols-outlined text-lg text-red-500" v-tooltip="'SSL Not Installed'" v-else>lock_open</span>
                </td>
                <td class="whitespace-nowrap py-4 px-4 text-sm text-center">
                    {{application.size}}
                </td>
                <td class="whitespace-nowrap py-4 px-4 text-sm text-center">
                    <Switch 
                        v-model="application.enable" 
                        @update:model-value="toggleApplication(application)"
                        :class="['relative inline-flex h-5 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out', application.enable ? 'bg-custom-500' : 'bg-gray-300']"
                    >
                        <span aria-hidden="true" :class="[application.enable ? 'translate-x-6' : 'translate-x-0', 'pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out']" />
                    </Switch>
                </td>
                <td class="whitespace-nowrap py-4 px-4 text-sm text-center">
                    <select
                        :disabled="!application.enable"
                        v-model="application.priority"
                        @change="changePriority(application)"
                        class="w-fit placeholder:font-italitc text-sm border focus:ring-0 rounded-md py-1.5 focus:outline-none"
                    >
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                </td>
            </tr>
            <template #pagination>
                <div class="flex flex-wrap gap-3 justify-end border-t px-4 py-2.5" v-if="pagination.total > pagination.per_page">
                    <Pagination :pagination="pagination" @page-change="fetchApplication" />
                </div>
            </template> 
        </Table>
        <template v-else>
            <TableSkeleton class="mt-5" :heads="10" v-if="refreshing || loading"/>
            <Table class="mt-5" :head="thead" v-else>
                <tr>
                    <td colspan="10" class="text-center text-sm px-6 py-4">
                        {{
                            refreshing ? "Please Wait" : "No Data Found"
                        }}
                    </td>
                </tr>
            </Table>
        </template>
    </div>
</template>

<script>
import { Switch } from '@headlessui/vue'

export default{
    data(){
        return{
            servers: [],
            applications: [],
            thead: [
                {title: 'Server'},
                {title: 'Name'},
                {title: 'Primary Domain'},
                {title: 'Framework'},
                {title: 'PHP Version', classes: 'text-center'},
                {title: 'Status', classes: 'text-center'},
                {title: 'SSL', classes: 'text-center'},
                {title: 'Size (MB)', classes: 'text-center'},
                {title: 'Monitor Log', classes: 'text-center', tooltip: 'Enabling this option allows the application to fetch logs for monitoring.'},
                {title: 'Log Priority', classes: 'text-center', tooltip: 'Set application priority for log fetching, choosing between High (10 minutes), Medium (20 minutes), and Low (30 minutes).'}
            ],
            query: {
                search: ''
            },
            currentServer: '',
            currentServerId: '',
            pagination: null,
            refreshing: false,
        }
    },
    components: {
        Switch
    },
    created(){
        this.verifyApi(this.fetchServers)
        this.debouncedList = this.$debounce(this.fetchApplication, 500)
    },
    watch:{
        currentServerId(val){
            let server = this.servers.find((server) => server.id == val)
            this.currentServer = server
        }
    },
    methods: {
        async fetchServers(){
            let url = `/admin/servers`
            this.refreshing = true
            await this.$axios.get(url).then(({data}) => {
                this.servers = data.servers
                if(data.servers.length){
                    this.currentServer = data.servers[0]
                    this.currentServerId = data.servers[0].id
                    this.fetchApplication()
                }
            }).catch(({ response: { data } }) => {
                this.$toast.error(data.message);
            })
        },
        async fetchApplication(page=''){
            this.refreshing = true
            let url = `/admin/servers/${this.currentServerId}/applications?pagination&search=${this.query.search}`

            if(page != ''){
                url = `${url}&page=${page}`
            }
            await this.$axios.get(url).then(({data}) => {
                this.pagination = data.applications
                this.applications = data.applications.data
            }).catch(({ response: { data } }) => {
                this.$toast.error(data.message);
            }).finally(() => {
                this.refreshing = false
            })
        },
        async changePriority(application){
            let url = `/admin/servers/${this.currentServerId}/applications/${application.id}/update-priority`

            await this.$axios.patch(url, {
                priority: application.priority
            }).then(({data}) => {
                this.$toast.success(data.message)
            }).catch(({ response: { data } }) => {
                this.$toast.error(data.message);
            }).finally(() => {
                this.fetchApplication()
            })
        },
        async toggleApplication(application){
            let url = `/admin/servers/${this.currentServerId}/applications/${application.id}/update-enable`

            await this.$axios.patch(url, {
                enable: application.enable ? true : false
            }).then(({data}) => {
                this.$toast.success(data.message)
            }).catch(({ response: { data } }) => {
                this.$toast.error(data.message);
            }).finally(() => {
                this.fetchApplication()
            })
        }
    }
}
</script>
