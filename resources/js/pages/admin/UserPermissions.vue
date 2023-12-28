<template>
    <div class="container-fluid">
        <div class="flex flex-wrap gap-4 justify-between items-center mt-3">
            <h1 class="text-2xl text-black">Permissions</h1>
            <div class="flex flex-wrap gap-3">
                <router-link :class="[isLightColor ? 'text-custom-700' : 'text-custom-500','flex items-center text-tiny bg-custom-100 rounded-md px-3 py-2']" :to="{name: 'adminUsers'}">
                    <span class="material-symbols-outlined">
                        chevron_left
                    </span>
                    Back to Users
                </router-link>
                <Button @click="openModal()" class="flex justify-center items-center gap-1">
                    Attach
                </Button>
            </div>
        </div>
        <div class="grid xl:grid-cols-4 md:grid-cols-3 grid-cols-1 gap-5 mt-5" v-if="user">
            <div class="bg-custom-500 rounded-md p-3">
                <div class="flex items-center gap-3">
                    <img :src="user.avatar" class="rounded-md w-9 h-9"/>
                    <div :class="[textColorClass, 'flex flex-col gap-0.5 text-tiny']">
                        <span>{{ user.name }}</span>
                        <span>{{ user.email }}</span>
                    </div>
                </div>
            </div>
            <div class="border border-neutral-200 rounded-md p-3">
                <div class="flex items-center gap-3 h-full">
                    <div :class="[isLightColor ? 'text-custom-700 bg-custom-200' : 'text-custom-500 bg-custom-100', 'rounded-md px-2 py-2 h-fulll flex items-center']">
                        <span class="material-symbols-outlined">
                            bottom_app_bar
                        </span>
                    </div>
                    <div class="flex flex-col gap-0.5 text-tiny">
                        <span>{{ user.designation }}</span>
                    </div>
                </div>
            </div>
        </div>
        <Table class="mt-5" :head="thead" v-if="permissions.length > 0">
            <tr class="even:bg-slate-50 last:border-0 border-b border-gray-300" v-for="permission in permissions" :key="permission.id">
                <td class="whitespace-nowrap py-4 px-4 text-sm">
                    {{ permission.server.name }}
                </td>
                <td class="whitespace-nowrap py-4 px-4 text-sm">
                    <div class="flex gap-2 items-center">
                        <div
                            :class="[isLightColor ? 'bg-custom-200 text-custom-700' : 'text-custom-500 bg-custom-100', 'px-2.5 py-1.5 rounded-md']"
                            v-for="application in permission.applications.slice(0, 5)"
                            :key="application.id"
                        >
                            {{ application.name }}
                        </div>
                        <button
                            @click="showPermissionModal(permission)"
                            :class="[isLightColor ? 'text-custom-700' : 'text-custom-500', 'text-sm rounded-md underline']"
                            v-if="permission.applications.length > 5"
                        >
                            View More
                        </button>
                    </div>
                </td>
                <td class="whitespace-nowrap py-4 px-4 text-sm">
                    <div class="flex gap-4 justify-end">
                        <button @click="openModal(permission, true)">
                            <span class="material-symbols-outlined text-xl text-green-500">
                                edit
                            </span>
                        </button>
                        <button v-tooltip="'Remove'" @click="openConfirmationModal(permission)">
                            <span class="material-symbols-outlined text-xl text-red-500">
                                delete
                            </span>
                        </button>
                    </div>
                </td>
            </tr>
            <template #pagination>
                <div class="flex flex-wrap gap-3 justify-end border-t px-4 py-2.5" v-if="pagination.total > pagination.per_page">
                    <Pagination :pagination="pagination" @page-change="fetchPermissions" />
                </div>
            </template>
        </Table>
        <template v-else>
            <TableSkeleton class="mt-5" :heads="3" v-if="refreshing"/>
            <Table class="mt-5" :head="thead" v-else>
                <tr>
                    <td colspan="3" class="text-center text-sm px-6 py-4">
                        {{
                            refreshing ? "Please Wait" : "No Data Found"
                        }}
                    </td>
                </tr>
            </Table>
        </template>

        <!-- Attch Permissions Modal -->
        <Modal
            :modalTitle="isUpdate ? 'Update Permissions' : 'Attach Permissions'"
            :customClass="['overflow-visible']"
            :openModal="showModal"
            @closeModal="closeModal()"
        >
            <form action="javascript:void(0)" @submit="attachPermssion()"  class="space-y-4">
                <div>
                    <label for="server_id" class="block text-tiny text-neutral-600 font-medium">
                        Select Server
                    </label>
                    <select :disabled="isUpdate" v-model="payload.server_id" @change="fetchApplications()" placeholder="Select Server" class="mt-1.5 block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0">
                        <option value="" disabled>Select Server</option>
                        <option 
                            class="capitalize"
                            v-for="server in servers" 
                            :key="server.id" 
                            :value="server.id"
                        >
                            {{server.name}}
                        </option>
                    </select>
                    <small id="server_id_message" class="error_message text-red-500"></small>
                </div>
                <div>
                    <label for="server_id" class="mb-2 block text-tiny text-neutral-600 font-medium">
                        Select Applications
                    </label>
                    <multiselect
                        v-model="payload.application_ids"
                        :options="applications"
                        :close-on-select="false"
                        :clear-on-select="false"
                        :multiple="true"
                        :searchable="true"
                        placeholder="Select Applications"
                        label="name"
                        track-by="value"
                    >
                    </multiselect>
                    <small id="application_ids_message" class="error_message text-red-500"></small>
                </div>
                <div class="text-end">
                    <Button class="mt-2" :disabled="processing">
                        <i v-if="processing" class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"></i>
                        {{processing ? 'Please Wait' : isUpdate ? 'Update' : 'Attach'}}
                    </Button>
                </div>
            </form>
        </Modal>

        <!-- View More Permission Modal -->
        <Modal
            :modalTitle="'Permissions'"
            :customClass="['xl:max-w-2xl']"
            :openModal="showPermission"
            @closeModal="closePermissionModal()"
        >
            <template v-if="viewPermissions">
                <CustomScrollbar class="max-h-52 min-h-[2.5rem]">
                    <label class="text-tiny text-gray-800">Server :</label>
                    <div :class="[isLightColor ? 'bg-custom-200 text-custom-700' : 'text-custom-500 bg-custom-100' , 'mb-3 mt-2 rounded-md text-tiny px-3 py-2 w-fit']">
                        {{ viewPermissions.server.name }}
                    </div>
                    <label class="text-tiny text-gray-800">Applications :</label>
                    <div class="flex flex-wrap gap-2">
                        <template v-for="application in viewPermissions.applications" :key="application.id">
                            <div :class="[isLightColor ? 'bg-custom-200 text-custom-700' : 'text-custom-500 bg-custom-100', 'mt-2 rounded-md text-tiny px-3 py-2 w-fit']">
                                {{ application.name }}
                            </div>
                        </template>
                    </div>
                </CustomScrollbar>
            </template>
        </Modal>

        <!-- Remove Permission Confirmation Modal -->
        <Confirmation
            confirmationTitle="Remove Permission"
            submitBtnTitle="Yes, I'm sure"
            :showLoader="showConfirmLoader"
            :show="showConfirmation"
            @confirm="deletePermissionFn"
            @closeModal="closeConfirmationModal"
        >
            <template #icon>
                <i
                    class="fas fa-warning h-6 w-6 text-xl text-red-600 text-center flex items-center justify-center"
                    aria-hidden="true"
                ></i>
            </template>
            <template #content>
                <p class="text-sm text-gray-600" v-if="deletePermission">
                    Are you sure you want to remove this permissions from user?
                </p>
                <div class="flex flex-co mt-2" v-if="deletePermission">
                    <div class="py-2 inline-block min-w-full">
                        <div class="overflow-hidden border rounded-md">
                            <table class="min-w-full">
                                <tbody>
                                    <tr class="border-b">
                                        <td class="p-2.5 whitespace-nowrap text-sm text-gray-900">Server:</td>
                                        <td class="text-sm text-gray-900 font-cera-pro-bold p-2.5 whitespace-nowrap">
                                            {{deletePermission.server.name}}
                                        </td>
                                    </tr>
                                    <tr class="">
                                        <td class="p-2.5 whitespace-nowrap text-sm text-gray-900">Applications:</td>
                                        <td class="text-sm text-gray-900 font-cera-pro-bold p-2.5 whitespace-nowrap">
                                            <div class="flex flex-wrap gap-2 items-center">
                                                <div
                                                    class="bg-custom-100 text-custom-500 px-2.5 py-1 rounded-md"
                                                    v-for="application in deletePermission.applications"
                                                    :key="application.id"
                                                >
                                                    {{ application.name }}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </template>
        </Confirmation>
    </div>
</template>

<script>
    import Multiselect from "vue-multiselect";
    export default{
        data(){
            return{
                thead: ['Server', 'Applications', ' '],
                servers: [],
                applications: [],
                permissions: [],
                user: null,
                pagination: null,
                payload: {
                    server_id: '',
                    application_ids: []
                },
                deletePermission: null,
                viewPermissions: null,
                refreshing: false,
                processing: false,
                showModal: false,
                isUpdate: false,
                showConfirmLoader: false,
                showConfirmation: false,
                showPermission: false
            }
        },
        components: {
            Multiselect
        },
        created(){
            this.fetchPermissions()
            this.fetchServers()
        },
        methods: {
            openModal(permission=null, isUpdate=false){
                this.showModal = true
                this.isUpdate = isUpdate
                if(permission){
                    this.payload.server_id = permission.server.id
                    this.fetchApplications()
                    this.payload.user_server_id = permission.id
                    permission.applications.map((app) => {
                        this.payload.application_ids.push({
                            value: app.id,
                            name: app.name
                        })
                    })
                }
            },
            closeModal(){
                if(this.isUpdate){
                    delete this.payload.user_server_id
                }
                this.showModal = false
                this.isUpdate = false
                this.payload = {
                    server_id : '',
                    application_ids: []
                }
            },
            showPermissionModal(permissions){
                this.showPermission = true
                this.viewPermissions = permissions
            },
            closePermissionModal(){
                this.showPermission = false
                this.viewPermissions = null
            },
            openConfirmationModal(permission) {
                this.deletePermission = permission;
                this.showConfirmation = !this.showConfirmation;
            },
            closeConfirmationModal() {
                this.showConfirmLoader = false;
                this.deletePermission = null;
                this.showConfirmation = false;
            },
            async fetchServers(){
                let url = `/admin/servers`
                await this.$axios.get(url).then(({data}) => {
                    this.servers = data.servers
                }).catch(({ response: { data } }) => {
                    this.$toast.error(data.message);
                })
            },
            async fetchApplications(){
                this.applications = []
                let url = `/admin/servers/${this.payload.server_id}/applications`
                await this.$axios.get(url).then(({data}) => {
                    data.applications.forEach((app) => {
                        let obj = {
                            value: app.id,
                            name: app.name
                        }
                        this.applications.push(obj)
                    })
                }).catch(({ response: { data } }) => {
                    this.$toast.error(data.message);
                })
            },
            async fetchPermissions(page=''){
                this.refreshing = true
                let url = `/admin/users/${this.$route.params.user}/permissions`

                if(page != ''){
                    url = `${url}?page=${page}`
                }
                await this.$axios.get(url).then(({data}) => {
                    this.pagination = data.permissions
                    this.permissions = data.permissions.data
                    this.user = data.user
                }).catch(({ response: { data } }) => {
                    this.$toast.error(data.message);
                }).finally(() => {
                    this.refreshing = false
                })
            },
            async attachPermssion(){
                this.hideError()
                this.processing = true

                let data = {}
                data.server_id = this.payload.server_id,
                data.user_server_id = this.payload.user_server_id
                data.application_ids = this.payload.application_ids.map((app) => {
                    return app.value
                });
                
                let url = `/admin/users/${this.$route.params.user}/attach-permission`;
                if(this.isUpdate){
                    url = `/admin/users/${this.$route.params.user}/update-permission`
                    data._method = 'PATCH'
                }

                await this.$axios.post(url, data).then(({data}) => {
                    this.$toast.success(data.message)
                    this.fetchPermissions()
                    this.closeModal()
                }).catch(({ response }) => {
                    if (response !== undefined) {
                        const { status, data } = response;
                        if (status === 422) {
                            this.displayError(data);
                        } else {
                            this.$toast.error(data.message);
                            this.closeModal()
                        }
                    }
                }).finally(() => {
                    this.processing = false
                })
            },
            async deletePermissionFn(){
                this.showConfirmLoader = true;
                await this.$axios.delete(`/admin/users/${this.$route.params.user}/delete-permission/${this.deletePermission.id}`)
                .then(({ data }) => {
                    this.$toast.success(data.message);
                    this.fetchPermissions();
                }).catch((error) => {
                    this.$toast.error(error.response.data.message);
                })
                .finally(() => {
                    this.closeConfirmationModal()
                });
            },
        }
    }   
</script>