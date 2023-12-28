<template>
    <div class="container-fluid">
        <div class="flex flex-wrap gap-4 justify-between items-center mt-3">
            <h1 class="text-2xl text-black">Servers</h1>
            <div class="flex gap-3">
                <label class="relative block text-[#8C8C8C]">
                    <span
                        class="absolute inset-y-0 left-0 flex items-center pl-3"
                    >
                        <i class="las la-search text-xl -rotate-90"></i>
                    </span>
                    <input
                        @input="debouncedList()"
                        v-model="query.search"
                        class="w-full h-full placeholder:font-italitc text-sm bg-white border-0 focus:ring-0 focus:border-sa-500 rounded-md py-3 pl-10 pr-4 focus:outline-none"
                        placeholder="Search"
                        type="text"
                    />
                </label>
                <button :class="[textColorClass, 'bg-custom-500 rounded-md px-2.5 py-0.5']" @click="fetchServers()">
                    <span class="material-symbols-outlined text-xl" :class="refreshing || loading ? 'fa-spin' : ''">
                        cached
                    </span>
                </button>
            </div>
        </div>
        <Table class="mt-5" :head="thead" v-if="servers.length > 0 && !refreshing && !loading">
            <tr class="even:bg-slate-50 last:border-0 border-b border-gray-300 " v-for="server in servers" :key="server.id">
                <td class="px-4 py-3 text-sm text-left">
                    <template v-if="server.agent_status == '1'">
                        <router-link 
                        :to="{
                            name: 'serverDashboard',
                            params: {
                                server: server.id
                            }
                        }">
                             {{ server.name }}
                        </router-link>
                    </template>
                    <template v-else>
                        {{ server.name }}
                    </template>
                </td>
                <td class="px-4 py-3 text-sm text-left">
                    {{ server.ip }}
                </td>
                <td class="px-4 py-3 text-sm text-center ">
                    <i class="fas fa-circle text-green-600" v-if="server.agent_status == '1'"></i>
                    <i class="fas fa-circle text-red-500" v-else></i>
                </td>
                <td class="px-4 py-3 text-sm text-left capitalize">
                    {{ server.web_server == 'apache2' ? 'Apache' : server.web_server}}
                </td>
                <td class="px-4 py-3 text-sm text-left">
                    Ubuntu {{ server.version }}
                </td>
                <td class="px-4 py-3 text-sm text-end">
                    <template v-if="server.agent_status == '1'">
                        <router-link 
                        :to="{
                            name: 'serverDashboard',
                            params: {
                                server: server.id
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
            <template #pagination>
                <div class="flex flex-wrap gap-3 justify-end border-t px-4 py-2.5" v-if="pagination.total > pagination.per_page">
                    <Pagination :pagination="pagination" @page-change="fetchServers" />
                </div>
            </template>
        </Table>
        <template v-else>
            <TableSkeleton class="mt-5" :heads="5" v-if="refreshing || loading"/>
            <Table class="mt-5" :head="thead" v-else>
                <tr>
                    <td colspan="5" class="text-center text-sm px-6 py-4">
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
            pagination: null,
            thead: [
                {title: 'Name'},
                {title: 'IP Address'},
                {title: 'Status', classes: 'text-center'},
                {title: 'Web Server'},
                {title: 'OS'},
                ''
            ],
            query: {
                search: ''
            },
            refreshing: false
        }
    },
    created(){
        this.verifyApi(this.fetchServers)
        this.debouncedList = this.$debounce(this.fetchServers, 500)
    },
    methods: {
        async fetchServers(page=''){
            this.refreshing = true
            let url = `/servers?per_page=10&pagination&search=${this.query.search}`

            if(page != ''){
                url = `${url}&page=${page}`
            }
            await this.$axios.get(url).then(({data}) => {
                this.pagination = data.servers
                this.servers = data.servers.data
            }).catch(({ response: { data } }) => {
                this.$toast.error(data.message);
            }).finally(() => {
                this.refreshing = false
            })
        }
    }
}
</script>
