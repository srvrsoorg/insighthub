<template>
    <div class="container-fluid">
        <h1 class="text-2xl text-black">Users</h1>
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
            <Button @click="openModal()" class="!py-1.5 flex justify-center items-center gap-1">
                <span class="material-symbols-outlined text-xl">
                    person_add
                </span>
                Add User
            </Button>
        </div>
        <Table class="mt-5" :head="thead" v-if="users.length > 0">
            <tr class="even:bg-slate-50 last:border-0 border-b border-gray-300" v-for="user in users" :key="user.id">
                <td class="whitespace-nowrap py-4 px-4 text-sm">
                    {{ user.name ? user.name : '-'}}
                </td>
                <td class="whitespace-nowrap py-4 px-4 text-sm">
                    {{ user.email }}
                </td>
                <td class="whitespace-nowrap py-4 px-4 text-sm">
                    {{ user.designation }}
                </td>
                <td class="whitespace-nowrap py-4 px-4 text-sm flex gap-4 justify-end">
                    <button v-tooltip="'Grant all Permission'" @click="openGrantAllPermissionModal(user)">
                        <span class="material-symbols-outlined text-xl text-black">
                            admin_panel_settings
                        </span>
                    </button>
                    <router-link :to="{
                        name: 'adminUserPermissions',
                        params: {
                            user: user.id
                        }
                    }" v-tooltip="'View Permissions'">
                        <span class="material-symbols-outlined text-xl text-blue-500">
                            visibility
                        </span>
                    </router-link>
                    <button v-tooltip="'Update User'" @click="openModal(user)">
                        <span class="material-symbols-outlined text-xl text-green-500">
                            edit
                        </span>
                    </button>
                    <button v-tooltip="'Remove User'" @click="openConfirmationModal(user)" v-if="!user.is_admin">
                        <span class="material-symbols-outlined text-xl text-red-500">
                            delete
                        </span>
                    </button>
                </td>
            </tr>
            <template #pagination>
                <div class="flex flex-wrap gap-3 justify-end border-t px-4 py-2.5" v-if="pagination.total > pagination.per_page">
                    <Pagination :pagination="pagination" @page-change="fetchUsers" />
                </div>
            </template>
        </Table>
        <template v-else>
            <TableSkeleton class="mt-5" :heads="4" v-if="refreshing || loading"/>
            <Table class="mt-5" :head="thead" v-else>
                <tr>
                    <td colspan="4" class="text-center text-sm px-6 py-4">
                        {{
                            refreshing ? "Please Wait" : "No Data Found"
                        }}
                    </td>
                </tr>
            </Table>
        </template>

        <!-- Add/Edit User Modal -->
        <Modal
            :modalTitle="isUpdateUser ? 'Update User' : 'Add User'"
            :openModal="showModal"
            @closeModal="closeModal()"
        >
            <form action="javascript:void(0)" @submit="addUser()"  class="space-y-3">
                <div class="grid xl:grid-cols-2 grid-cols-1 gap-3">
                    <div>
                        <label for="name" class="block text-tiny text-neutral-600 font-medium">
                            Name
                        </label>
                        <div class="mt-1.5">
                            <input
                                id="name"
                                name="name"
                                type="text"
                                placeholder="Enter Name"
                                v-model="user.name" 
                                class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                            />
                            <small id="name_message" class="error_message text-red-500"></small>
                        </div>
                    </div>
                    <div>
                        <label for="email" class="block text-tiny text-neutral-600 font-medium">
                            Email
                        </label>
                        <div class="mt-1.5">
                            <input
                                id="email"
                                name="email"
                                type="email"
                                placeholder="Enter Email"
                                v-model="user.email" 
                                class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                            />
                            <small id="email_message" class="error_message text-red-500"></small>
                        </div>
                    </div>
                </div>
                <div>
                    <label for="designation" class="block text-tiny text-neutral-600 font-medium">
                        Designation
                    </label>
                    <div class="mt-1.5">
                        <input
                            id="designation"
                            name="designation"
                            type="text"
                            placeholder="Enter Designation"
                            v-model="user.designation" 
                            class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                        />
                        <small id="designation_message" class="error_message text-red-500"></small>
                    </div>
                </div>
                <div>
                    <label for="password" class="block text-tiny text-neutral-600 font-medium">
                        Password
                    </label>
                    <div class="mt-1.5">
                        <div class="relative">
                            <input
                                id="password"
                                name="password"
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="Enter Password"
                                v-model="user.password" 
                                :class="{'tracking-widest': !showPassword}"
                                class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal text-sm leading-6 focus:ring-0"
                            />
                            <PasswordVisibility :showPassword="showPassword" @toggle="showPassword = !showPassword"/>
                        </div>
                        <small id="password_message" class="error_message text-red-500"></small>
                    </div>
                </div>
                <div v-if="!isUpdateUser">
                    <label for="password_confirmation" class="block text-tiny text-neutral-600 font-medium">
                        Confirm Password
                    </label>
                    <div class="mt-1.5">
                        <div class="relative">
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                :type="showConfirmPassword ? 'text' : 'password'"
                                placeholder="Enter Confirm Password"
                                v-model="user.password_confirmation" 
                                :class="{'tracking-widest': !showConfirmPassword}"
                                class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal text-sm leading-6 focus:ring-0"
                            />
                            <PasswordVisibility :showPassword="showConfirmPassword" @toggle="showConfirmPassword = !showConfirmPassword"/>
                        </div>
                        <small id="password_confirmation_message" class="error_message text-red-500"></small>
                    </div>
                </div>
                <div class="text-end">
                    <Button class="mt-2" :disabled="processing">
                        <i v-if="processing" class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"></i>
                        {{processing ? 'Please Wait' : isUpdateUser ? 'Update' : 'Add'}}
                    </Button>
                </div>
            </form>
        </Modal>

        <!-- Remove User Confirmation Modal -->
        <Confirmation
            confirmationTitle="Remove User"
            submitBtnTitle="Yes, I'm sure"
            :showLoader="showConfirmLoader"
            :show="showConfirmation"
            @confirm="deleteUserFn"
            @closeModal="closeConfirmationModal"
        >
            <template #icon>
                <i
                    class="fas fa-warning h-6 w-6 text-xl text-red-600 text-center flex items-center justify-center"
                    aria-hidden="true"
                ></i>
            </template>
            <template #content>
                <p class="text-sm text-gray-600" v-if="deleteUser">
                    Are you sure you want to remove this user ({{ deleteUser.email }}) ?
                </p>
            </template>
        </Confirmation>

        <!-- Give all Permission Confirmation Modal -->
        <Confirmation
            confirmationTitle="Grant all Permissions"
            submitBtnTitle="Yes, I'm sure"
            :showLoader="grantAllPermission.showConfirmLoader"
            :show="grantAllPermission.showConfirmation"
            @confirm="grantAllPermissionFn"
            @closeModal="closeGrantAllPermissionModal"
        >
            <template #icon>
                <i
                    class="fas fa-warning h-6 w-6 text-xl text-red-600 text-center flex items-center justify-center"
                    aria-hidden="true"
                ></i>
            </template>
            <template #content>
                <p class="text-sm text-gray-600" v-if="grantAllPermission.user">
                    Are you sure you want to Grant all server and application permissions to this user?
                </p>
                <div class="flex flex-co mt-2" v-if="grantAllPermission.user">
                    <div class="py-2 inline-block min-w-full">
                        <div class="overflow-hidden border rounded-md">
                            <table class="min-w-full">
                                <tbody class="w-full">
                                    <tr class="border-b">
                                        <td class="p-2.5 whitespace-nowrap text-sm text-gray-900">Name:</td>
                                        <td class="text-sm text-gray-800 p-2.5 whitespace-nowrap">
                                            {{grantAllPermission.user.name}}
                                        </td>
                                    </tr>
                                    <tr class="">
                                        <td class="p-2.5 whitespace-nowrap text-sm text-gray-900">Email:</td>
                                        <td class="text-sm text-gray-800 p-2.5 whitespace-nowrap">
                                            {{grantAllPermission.user.email}}
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
export default{
    data(){
        return{
            users: [],
            thead: [
                {title: 'Name'},
                {title: 'Email'},
                {title: 'Designation'},
                ''
            ],
            user: { 
                email: '',
                role: '',
                password: '',
                password_confirmation: '',
                designation: ''
            },
            query: {
                search: ''
            },
            isUpdateUser: false,
            showPassword: false,
            showConfirmPassword: false,
            deleteUser: null,
            pagination: null,
            showConfirmLoader: false,
            showConfirmation: false,
            grantAllPermission: {
                showConfirmLoader: false,
                showConfirmation: false,
                user: null
            },
            refreshing: false,
            processing: false,
            showModal: false
        }
    },
    created(){
        this.verifyApi(this.fetchUsers)
        this.debouncedList = this.$debounce(this.fetchUsers, 500)
    },
    methods: {
        openModal(user=null){
            if(user){
                this.user.name = user.name
                this.user.email = user.email
                this.user.designation = user.designation
                this.user.id = user.id
                this.isUpdateUser = true
            }
            this.showModal = true
        },
        closeModal(){
            this.showModal = false
            this.resetData()
        },
        openConfirmationModal(user) {
            this.deleteUser = user;
            this.showConfirmation = !this.showConfirmation;
        },
        closeConfirmationModal() {
            this.showConfirmLoader = false;
            this.deleteUser = null;
            this.showConfirmation = false;
        },
        openGrantAllPermissionModal(user){
            this.grantAllPermission.user = user
            this.grantAllPermission.showConfirmation = true
        },
        closeGrantAllPermissionModal(){
            this.grantAllPermission.user = null
            this.grantAllPermission.showConfirmation = false
            this.grantAllPermission.showConfirmLoader = false
        },
        resetData(){
            this.user = {
                email: '',
                role: '',
                password: '',
                password_confirmation: '',
                designation: ''
            }
            if(this.isUpdateUser){
                delete this.user.id
            }
            this.isUpdateUser = false
        },
        async fetchUsers(page=''){
            this.refreshing = true
            let url = `/admin/users?pagination&search=${this.query.search}`

            if(page != ''){
                url = `${url}&page=${page}`
            }
            await this.$axios.get(url).then(({data}) => {
                this.pagination = data.users
                this.users = data.users.data
            }).catch(({ response: { data } }) => {
                this.$toast.error(data.message);
            }).finally(() => {
                this.refreshing = false
            })
        },
        async addUser(){
            this.hideError()
            this.processing = true

            let url = '/admin/users'

            let payload = {...this.user}
            if(this.isUpdateUser){
                payload._method = 'PATCH'
                url = `/admin/users/${this.user.id}`
            }

            await this.$axios.post(url, payload).then(({data}) => {
                this.$toast.success(data.message)
                this.fetchUsers()
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
        async deleteUserFn(){
            this.showConfirmLoader = true;
            await this.$axios.delete(`/admin/users/${this.deleteUser.id}`)
            .then(({ data }) => {
                this.$toast.success(data.message);
                this.fetchUsers();
            }).catch((error) => {
                this.$toast.error(error.response.data.message);
            })
            .finally(() => {
                this.closeConfirmationModal()
            });
        },
        async grantAllPermissionFn(){
            this.grantAllPermission.showConfirmLoader = true;
            await this.$axios.get(`/admin/users/${this.grantAllPermission.user.id}/sync-all-permission`)
            .then(({ data }) => {
                this.$toast.success(data.message);
                this.fetchUsers();
            }).catch((error) => {
                this.$toast.error(error.response.data.message);
            })
            .finally(() => {
                this.closeGrantAllPermissionModal()
            });
        }
    }
}
</script>
