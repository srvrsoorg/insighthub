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
            SMTP Credentials
        </h2>
        <p class="text-sm text-gray-500 mt-2">Get SMTP credentials from your email provider for notifications or skip this step for later.</p>
        <div class="grid md:grid-cols-2 grid-cols-1 gap-3 mt-5">
            <InstallationInfo />
            <form action="javascript:void(0)" @submit="saveSmtpDetails()">
                <div class="grid md:grid-cols-2 grid-cols-1 gap-x-3">
                    <div>
                        <label for="username" class="block text-tiny text-neutral-600 font-medium"
                            >Username</label
                        >
                        <div class="mt-1.5">
                            <input
                                type="text"
                                name="username"
                                v-model="smtp.username"
                                id="username"
                                class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                                placeholder="Enter Username"
                            />
                            <small
                                class="text-red-500 error_message"
                                id="username_message"
                            ></small>
                        </div>
                    </div>
                    <div class="md:mt-0 mt-4">
                        <label for="password" class="block text-tiny text-neutral-600 font-medium"
                            >Password</label
                        >
                        <div class="mt-1.5">
                            <input
                                type="password"
                                name="password"
                                v-model="smtp.password"
                                id="password"
                                class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal tracking-widest text-sm leading-6 focus:ring-0"
                                placeholder="Enter Password"
                            />
                            <small
                                class="text-red-500 error_message"
                                id="password_message"
                            ></small>
                        </div>
                    </div>
                </div>
                <div class="grid md:grid-cols-2 grid-cols-1 gap-x-3 mt-4">
                    <div class="">
                        <label for="from_name" class="block text-tiny text-neutral-600 font-medium"
                            >From Name</label
                        >
                        <div class="mt-1.5">
                            <input
                                type="text"
                                name="from_name"
                                v-model="smtp.from_name"
                                id="from_name"
                                class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                                placeholder="Enter From Name"
                            />
                            <small
                                class="text-red-500 error_message"
                                id="from_name_message"
                            ></small>
                        </div>
                    </div>
                    <div class="md:mt-0 mt-4">
                        <label for="from_email" class="block text-tiny text-neutral-600 font-medium"
                            >From Email</label
                        >
                        <div class="mt-1.5">
                            <input
                                type="text"
                                name="from_email"
                                v-model="smtp.from_email"
                                id="from_email"
                                class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                                placeholder="Enter From Email"
                            />
                            <small
                                class="text-red-500 error_message"
                                id="from_email_message"
                            ></small>
                        </div>
                    </div>
                </div>
                <div class="grid md:grid-cols-2 grid-cols-1 gap-x-3 mt-4">
                    <div class="">
                        <label for="host" class="block text-tiny text-neutral-600 font-medium"
                            >Host</label
                        >
                        <div class="mt-1.5">
                            <input
                                type="text"
                                name="host"
                                v-model="smtp.host"
                                id="host"
                                class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                                placeholder="Enter Host"
                            />
                            <small
                                class="text-red-500 error_message"
                                id="host_message"
                            ></small>
                        </div>
                    </div>
                    <div class="md:mt-0 mt-4">
                        <label for="port" class="block text-tiny text-neutral-600 font-medium"
                            >Port</label
                        >
                        <div class="mt-1.5">
                            <input
                                type="text"
                                name="port"
                                v-model="smtp.port"
                                id="port"
                                class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                                placeholder="Enter Port"
                            />
                            <small
                                class="text-red-500 error_message"
                                id="port_message"
                            ></small>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <label for="encryption" class="block text-tiny text-neutral-600 font-medium"
                        >Encryption</label
                    >
                    <div class="mt-1.5">
                        <input
                            type="text"
                            name="encryption"
                            v-model="smtp.encryption"
                            id="encryption"
                            class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                            placeholder="Enter Encryption"
                        />
                        <small
                            class="text-red-500 error_message"
                            id="encryption_message"
                        ></small>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3 justify-between mt-6">
                    <router-link class="text-custom-500 border border-custom-500 rounded-md px-3 py-1.5 text-sm font-medium flex items-center" :to="{
                        name: 'keyVerification'
                    }">
                        <span class="material-symbols-outlined -ml-1.5"> chevron_left</span>
                        Back
                    </router-link>
                    <div class="flex flex-wrap gap-3">
                        <button type="button" @click="skip()" class="disabled:opacity-75 disabled:pointer-events-none text-indigo-500 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-0 font-medium rounded text-sm px-5 py-2 text-center">
                            Skip
                        </button>
                        <Button :disabled="processing">
                            <i v-if="processing" class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"></i>
                            {{processing ? 'Please Wait' : 'Next'}}
                        </Button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { mapState } from 'pinia';
import {useSetupStore} from '@/store/setup.js'
import { defineAsyncComponent } from 'vue';

export default{
    data(){
        return{
            smtp: {
                host: '',
                port: '',
                username: '',
                password: '',
                from_name: '',
                from_email: '',
                encryption: ''
            },
            processing: false
        }
    },
    computed:{
        ...mapState(useSetupStore, ['keyVerificationComplete'])
    },
    components:{
        InstallationInfo: defineAsyncComponent(() => import('@/components/InstallationInfo.vue'))
    },
    created(){
        if(!this.keyVerificationComplete){
            this.$router.push({
                name: 'keyVerification'
            })
        }else{
            this.getSmtpDetails()
        }
    },
    methods: {
        skip(){
            this.$router.push({
                name: 'register'
            })
        },
        async getSmtpDetails(){
            this.$axios.get(`/setup/smtp`).then(({data}) => {
                if(data.smtp){
                    this.smtp = data.smtp
                }
            }).catch(({ response: { data } }) => {})
        },
        async saveSmtpDetails(){
            this.processing = true
            this.hideError();

            this.$axios.post(`/setup/smtp`, this.smtp).then(({data}) => {
                this.$toast.success(data.message);
                this.skip()
            }).catch(({ response }) => {
                if (response !== undefined) {
                    const { status, data } = response;
                    if (status === 422) {
                        this.displayError(data);
                    } else {
                        this.$toast.error(data.message);
                    }
                }
            })
            .finally(() => {
                this.processing = false;
            });
        },
    }
}
</script>
