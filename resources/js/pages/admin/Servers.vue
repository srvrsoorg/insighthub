<template>
    <div class="container-fluid">
        <h1 class="text-2xl text-black">Servers</h1>
        <div class="flex flex-wrap gap-4 justify-between items-center mt-3">
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
            <Button @click="syncServer" :disabled="syncing" class="!py-1.5 flex justify-center items-center gap-1">
                <span class="material-symbols-outlined text-lg" :class="syncing? 'fa-spin': ''">
                    autorenew
                </span>
                {{ syncing ? 'Syncing' : 'Sync'}}
            </Button>
        </div>
        <Table class="mt-5" :head="thead" v-if="servers.length > 0">
            <tr class="even:bg-slate-50 last:border-0 border-b border-gray-300" v-for="server in servers" :key="server.id">
                <td class="whitespace-nowrap py-4 px-4 text-sm">
                    {{ server.name }}
                </td>
                <td class="whitespace-nowrap py-4 px-4 text-sm">
                    {{ server.ip }}
                </td>
                <td class="whitespace-nowrap py-4 px-4 text-sm text-center">
                    <i class="fas fa-circle text-green-600" v-if="server.agent_status == '1'"></i>
                    <i class="fas fa-circle text-red-500" v-else></i>
                </td>
                <td class="whitespace-nowrap py-4 px-4 text-sm capitalize">
                    {{ server.database == 'mysql' ? 'Mysql' : 'MariaDB'}}
                </td>
                <td class="whitespace-nowrap py-4 px-4 text-sm capitalize">
                    {{ server.web_server == 'apache2' ? 'Apache' : server.web_server}}
                </td>
                <td class="whitespace-nowrap py-4 px-4 text-sm">
                    Ubuntu {{ server.version }}
                </td>
            </tr>
            <template #pagination>
                <div class="flex flex-wrap gap-3 justify-end border-t px-4 py-2.5" v-if="pagination.total > pagination.per_page">
                    <Pagination :pagination="pagination" @page-change="fetchServers" />
                </div>
            </template>
        </Table>
        <template v-else>
            <TableSkeleton class="mt-5" :heads="6" v-if="refreshing || loading"/>
            <Table class="mt-5" :head="thead" v-else>
                <tr>
                    <td colspan="6" class="text-center text-sm px-6 py-4">
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
            thead: [
                {title: 'Name'},
                {title: 'IP Address'},
                {title: 'Status', classes: 'text-center'},
                {title: 'Database'},
                {title: 'Web Server'},
                {title: 'OS'}
            ],
            query: {
                search: ''
            },
            pagination: null,
            refreshing: false,
            syncing: false
        }
    },
    created(){
        this.verifyApi(this.fetchServers)
        this.debouncedList = this.$debounce(this.fetchServers, 500)
    },
    methods: {
        async syncServer(){
            this.syncing = true
            
            await this.$axios.get('/admin/sync').then(({data}) => {
                this.$toast.success(data.message)
                this.fetchServers()
            }).catch(({ response: { data } }) => {
                this.$toast.error(data.message);
            }).finally(() => {
                this.syncing = false
            })
        },
        async fetchServers(page=''){
            this.refreshing = true
            let url = `/admin/servers?pagination&search=${this.query.search}`

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
