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
            Registration
        </h2>
        <p class="text-sm text-gray-500 mt-2">Register as the admin for complete control of InsightHub.</p>
        <div class="grid md:grid-cols-2 grid-cols-1 gap-3 mt-5">
            <InstallationInfo />
            <form action="javascript:void(0)" @submit="register()">
                <div class="grid grid-cols-1 gap-x-5">
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
                                v-model="admin.name" 
                                class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                            />
                            <small id="name_message" class="error_message text-red-500"></small>
                        </div>
                    </div>
                    <div class="mt-4">
                        <label for="email" class="block text-tiny text-neutral-600 font-medium">
                            Email
                        </label>
                        <div class="mt-1.5">
                            <input
                                id="email"
                                name="email"
                                type="email"
                                placeholder="Enter Email"
                                v-model="admin.email" 
                                class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                            />
                            <small id="email_message" class="error_message text-red-500"></small>
                        </div>
                    </div>
                    <div class="mt-4">
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
                                    v-model="admin.password" 
                                    :class="{'tracking-widest': !showPassword}"
                                    class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal text-sm leading-6 focus:ring-0"
                                />
                                <PasswordVisibility :showPassword="showPassword" @toggle="showPassword = !showPassword"/>
                            </div>
                            <small id="password_message" class="error_message text-red-500"></small>
                        </div>
                    </div>
                    <div class="mt-4">
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
                                    v-model="admin.password_confirmation" 
                                    :class="{'tracking-widest': !showConfirmPassword}"
                                    class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal text-sm leading-6 focus:ring-0"
                                />
                                <PasswordVisibility :showPassword="showConfirmPassword" @toggle="showConfirmPassword = !showConfirmPassword"/>
                            </div>
                            <small id="password_confirmation_message" class="error_message text-red-500"></small>
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap justify-end gap-x-5 mt-6">
                    <div class="flex justify-between items-center">
                        <router-link class="text-custom-500 border border-custom-500 rounded-md px-3 py-1.5 text-sm font-medium flex items-center" :to="{
                            name: 'setupSmtp'
                        }">
                            <span class="material-symbols-outlined -ml-1.5"> chevron_left</span>
                            Back
                        </router-link>
                    </div>
                    <Button :disabled="processing">
                        <i v-if="processing" class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"></i>
                        {{processing ? 'Please Wait' : 'Register'}}
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import {mapActions, mapState} from 'pinia'
import {useAuthStore} from '@/store/auth'
import {useSetupStore} from '@/store/setup.js'
import { defineAsyncComponent } from 'vue';

export default{
    data(){
        return{
            admin: {
                name: '',
                email: '',
                password: '',
                password_confirmation: ''
            },
            processing: false,
            showPassword: false,
            showConfirmPassword: false
        }
    },
    created(){
        if(!this.keyVerificationComplete){
            this.$router.push({
                name: 'keyVerification'
            })
        }

        if(this.registerComplete){
            this.$router.push({
                name: 'setupSiteSettings'
            })
        }
    },
    components:{
        InstallationInfo: defineAsyncComponent(() => import('@/components/InstallationInfo.vue'))
    },
    computed:{
        ...mapState(useSetupStore, ['keyVerificationComplete', 'registerComplete'])
    },
    methods: {
        ...mapActions(useAuthStore, ['setAuthenticated', 'setAccessToken', 'setUser', 'setIsAdmin']),
        async register(){
            this.hideError()
            this.processing = true
            await this.$axios.post('/setup/register', this.admin).then(({data}) => {
                this.$toast.success(data.message)
                this.setAccessToken(data.token)
                this.setUser(data.user)
                this.setIsAdmin(data.is_admin)
                this.setAuthenticated(true)
                location.reload()
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
            })
        }
    }
}
</script>
