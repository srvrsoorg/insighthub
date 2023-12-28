<template>
    <div class="container-fluid">
        <div class="md:flex items-center gap-5 md:flex-nowrap flex-wrap mt-3 justify-between">
            <h1 class="text-2xl text-black">Applications</h1>
            <div class="md:flex justify-end xl:w-1/2 md:mt-0 md:flex-nowrap flex-wrap gap-x-5 gap-y-3 mt-5">
                <div class="flex gap-x-5 gap-y-3 justify-end items-center">
                    <select
                        v-if="servers.length && !$route.meta.showNarrowSidebar"
                        @change="fetchApplication()"
                        class="w-full placeholder:font-italitc text-sm border-0 bg-white focus:ring-0 rounded-md py-2.5 focus:outline-none"
                        v-model="currentServerId"
                    >
                        <option value="">All Server</option>
                        <option class="text-gray-800" v-for="server in servers" :key="server.id" :value="server.id">{{server.name}} ({{server.ip}})</option>
                    </select>
                    <label 
                        class="relative justify-self-end block text-[#8C8C8C] w-full"
                    >
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="las la-search text-xl -rotate-90"></i>
                        </span>
                        <input
                            @input="debouncedList()"
                            v-model="query.search"
                            class="h-full w-full placeholder:font-italitc text-sm bg-white border-0 focus:ring-0 rounded-md py-3 pl-10 pr-4 focus:outline-none"
                            placeholder="Search"
                            type="text"
                        />
                    </label>
                </div>
                <button 
                    class="bg-custom-500 rounded-md px-2.5 py-1 mt-3" 
                    :class="[$route.meta.showNarrowSidebar ? 'sm:mt-0' : 'md:mt-0', textColorClass]"
                    @click="fetchServers()"
                >
                    <span class="material-symbols-outlined text-xl" :class="refreshing || loading ? 'fa-spin' : ''">
                        cached
                    </span>
                </button>
            </div>
        </div>
        <Table class="mt-5" :head="thead" v-if="applications.length > 0 && !refreshing && !loading">
            <tr class="even:bg-slate-50 last:border-0 border-b border-gray-300 " v-for="application in applications" :key="application.id">
                <td class="px-4 py-3 text-sm flex flex-col gap-1">
                    <span>{{ application.server.name }}</span>
                    <span> ({{ application.server.ip }})</span> 
                </td>
                <td class="px-4 py-3 text-sm max-w-[150px] overflow-auto truncate">
                    <template v-if="application.server.agent_status == '1'">
                        <router-link 
                        v-tooltip="application.name"
                        :to="{
                            name: 'applicationDashboard',
                            params: {
                                server: application.server.id,
                                application: application.id
                            }
                        }">
                            {{ application.name }}
                        </router-link>
                    </template>
                    <template v-else>
                        {{ application.name }}
                    </template>
                </td>
                <td class="px-4 py-3 text-sm">
                    {{ application.primary_domain }}
                    <a :href="'http://' + application.primary_domain" target="_blank" :class="[isLightColor ? 'text-custom-700' : 'text-custom-500']">
                        <i class="las la-external-link-alt"></i>
                    </a>
                </td>
                <td class="px-4 py-3 text-sm">
                    {{ application.framework }}
                </td>
                <td class="px-4 py-3 text-sm text-center">
                    <span v-if="application.php_version == 8">8.0</span>
                    <span v-else-if="application.php_version == 7">7.0</span>
                    <span v-else>{{ application.php_version }}</span>
                </td>
                <td class="px-4 py-3 text-sm text-center">
                    <i class="fas fa-circle text-green-600" v-if="application.active"></i>
                    <i class="fas fa-circle text-red-500" v-else></i>
                </td>
                <td class="px-4 py-3 text-sm text-center">
                    <span class="material-symbols-outlined text-lg text-green-600" v-tooltip="'SSL Installed'" v-if="application.ssl">lock</span>
                    <span class="material-symbols-outlined text-lg text-red-500" v-tooltip="'SSL Not Installed'" v-else>lock_open</span>
                </td>
                <td class="px-4 py-3 text-sm text-center">
                    {{application.size.toFixed(3)}}
                </td>
                <td class="px-4 py-3 text-sm">
                    <template v-if="application.server.agent_status == '1'">
                        <router-link 
                        :to="{
                            name: 'applicationDashboard',
                            params: {
                                server: application.server.id,
                                application: application.id
                            }
                        }">
                            <span :class="[isLightColor ? 'text-custom-700' : 'text-custom-500' , 'material-symbols-outlined']">
                                monitoring
                            </span>
                        </router-link>
                    </template>
                    <template v-else>
                        <span class="material-symbols-outlined text-gray-500">
                            monitoring
                        </span>
                    </template>
                </td>
            </tr>
        </Table>
        <template v-else>
            <TableSkeleton class="mt-5" :heads="9" v-if="refreshing || loading"/>
            <Table class="mt-5" :head="thead" v-else>
                <tr>
                    <td colspan="9" class="text-center text-sm px-6 py-4">
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
                ''
            ],
            query: {
                search: ''
            },
            currentServerId: '',
            pagination: null,
            refreshing: false,
        }
    },
    watch: {
        '$route'(){
            this.verifyApi(this.fetchData)
        }
    },
    created(){
        this.verifyApi(this.fetchData)
        this.debouncedList = this.$debounce(this.fetchApplication, 500)
    },
    methods: {
        async fetchData(){
            this.currentServerId = this.$route.meta.showNarrowSidebar ? this.$route.params.server : '';

            if(this.$route.meta.showNarrowSidebar){
                await this.fetchApplication()
            }else{
                await this.fetchServers()
            }
        },
        async fetchServers(){
            let url = `/servers`
            this.refreshing = true
            await this.$axios.get(url).then(({data}) => {
                this.servers = data.servers
                this.fetchApplication()
            }).catch(({ response: { data } }) => {
                this.$toast.error(data.message);
            })
        },
        async fetchApplication(){
            this.refreshing = true
            let url = `/applications?search=${this.query.search}&server_id=${this.currentServerId}`

            await this.$axios.get(url).then(({data}) => {
                this.applications = data.applications
            }).catch(({ response: { data } }) => {
                this.$toast.error(data.message);
            }).finally(() => {
                this.refreshing = false
            })
        }
    }
}
</script>
