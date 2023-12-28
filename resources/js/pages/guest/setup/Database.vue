<template>
    <div class="w-full max-w-3xl mx-auto bg-white sm:px-8 px-6 py-6 shadow rounded-lg">
        <div class="sm:w-full sm:max-w-md">
            <img
                v-if="!logo"
                class="h-12 w-auto"
                src="/logo/logo2.png"
                :alt="app_name"
            />
            <img
                v-else
                class="h-12 w-auto"
                :src="logo"
                :alt="app_name"
            />
        </div>
        <h2 class="text-2xl mt-7">
            Database Credentials
        </h2>
        <p class="text-sm text-gray-500 mt-2">
            Essential for managing analytics data, a MySQL or MariaDB database connection is Vital. 
        </p>
        <p class="text-gray-500 text-sm mt-1"><a href="https://serveravatar.com/docs/database/create" target="_blank" class="text-custom-500 underline">Create it effortlessly from ServerAvatar</a> or elsewhere.</p>
        <div class="grid md:grid-cols-2 grid-cols-1 gap-5 mt-5">
            <InstallationInfo />
            <form action="javascript:void(0)" @submit="saveDatabaseDetails()">
                <div>
                    <label for="host" class="block text-tiny text-neutral-600 font-medium">
                        Database Host
                    </label>
                    <div class="mt-1.5">
                        <input
                            id="host"
                            name="host"
                            type="text"
                            placeholder="Enter Database Host"
                            v-model="database.host" 
                            class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                        />
                        <small id="host_message" class="error_message text-red-500"></small>
                    </div>
                </div>
                <div class="mt-4">
                    <label for="database" class="block text-tiny text-neutral-600 font-medium">
                        Database Name
                    </label>
                    <div class="mt-1.5">
                        <input
                            id="database"
                            name="database"
                            type="text"
                            placeholder="Enter Database Name"
                            v-model="database.database" 
                            class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                        />
                        <small id="database_message" class="error_message text-red-500"></small>
                    </div>
                </div>
                <div class="mt-4">
                    <label for="username" class="block text-tiny text-neutral-600 font-medium">
                        Database Username
                    </label>
                    <div class="mt-1.5">
                        <input
                            id="username"
                            name="username"
                            type="text"
                            placeholder="Enter Database Username"
                            v-model="database.username" 
                            class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                        />
                        <small id="username_message" class="error_message text-red-500"></small>
                    </div>
                </div>
                <div class="mt-4">
                    <label for="password" class="block text-tiny text-neutral-600 font-medium">
                        Database Password
                    </label>
                    <div class="mt-1.5">
                        <div class="relative">
                            <input
                                id="password"
                                name="password"
                                :type="showPassword ? 'text' : 'password'"
                                placeholder="Enter Database Password"
                                v-model="database.password"
                                :class="{'tracking-widest': !showPassword}"
                                class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal text-sm leading-6 focus:ring-0"
                            />
                            <PasswordVisibility :showPassword="showPassword" @toggle="showPassword = !showPassword"/>
                        </div>
                        <small id="password_message" class="error_message text-red-500"></small>
                    </div>
                </div>
                <div class="flex flex-wrap justify-end gap-3 mt-6">
                    <router-link class="text-custom-500 border border-custom-500 rounded-md px-3 py-1.5 text-sm font-medium flex items-center" :to="{
                        name: 'checkPermissions'
                    }">
                        <span class="material-symbols-outlined -ml-1.5"> chevron_left</span>
                        Back
                    </router-link>
                    <Button :disabled="processing">
                        <i v-if="processing" class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"></i>
                        {{processing ? 'Please Wait' : 'Next'}}
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { mapState } from 'pinia';
import {useSetupStore} from '@/store/setup'
import { defineAsyncComponent } from 'vue';
export default{
    data(){
        return{
            database: {
                host: '',
                database: '',
                username: '',
                password: ''
            },
            processing: false,
            showPassword: false
        }
    },
    computed:{
        ...mapState(useSetupStore, ['permissionComplete'])
    },
    components:{
        InstallationInfo: defineAsyncComponent(() => import('@/components/InstallationInfo.vue'))
    },
    created(){
        if(!this.permissionComplete){
            this.$router.push({
                name: 'checkPermissions'
            })
        }else{
            this.fetchDatabaseDetails()
        }
    },
    methods: {
        async fetchDatabaseDetails(){
            await this.$axios.get("/setup/database").then(({ data }) => {
                if(data.database){
                    let { host, database, username, password } = data.database
                    Object.assign(this.database, { host, database, username, password })
                }
            })
        },
        async saveDatabaseDetails(){
            this.hideError();
            this.processing = true

            await this.$axios.post("/setup/database", this.database).then(({ data }) => {
                this.$toast.success(data.message);
                this.$router.push({
                    name: 'keyVerification'
                })
            }).catch(({ response }) => {
                if (response !== undefined) {
                    const { status, data } = response;
                    if (status === 422) {
                        this.displayError(data);
                    } else {
                        this.$toast.error(data.message);
                    }
                }
            }).finally(() => {
                this.processing = false
            });
        }
    }
}
</script>