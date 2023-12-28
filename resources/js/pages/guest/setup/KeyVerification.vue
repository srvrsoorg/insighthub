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
            License Key Verification
        </h2>
        <p class="text-sm text-gray-500 mt-2">Enter the key for advanced server and application analytics and monitoring.</p>
        <div class="grid md:grid-cols-2 grid-cols-1 gap-3 mt-5">
            <InstallationInfo />
            <form action="javascript:void(0)" @submit="saveKeys()">
                <span class="material-symbols-outlined text-4xl">
                    follow_the_signs
                </span>
                <p class="text-tiny">To obtain your license key, follow these steps:</p>
                <div class="flex items-center gap-4 text-sm mt-4">
                    <span :class="['bg-custom-500 ring-1 ring-custom-500 ring-offset-2 text-white tabular-nums flex items-center justify-center px-2 py-0.5 rounded-full']">
                       1
                    </span>
                    <span class="text-gray-500">Log in to your ServerAvatar account.</span>
                </div>
                <div class="flex items-center gap-4 text-sm mt-4">
                    <span :class="['bg-custom-500 ring-1 ring-custom-500 ring-offset-2 text-white flex items-center justify-center px-2 py-0.5 rounded-full']">
                       2
                    </span>
                    <span class="text-gray-500">Navigate to your profile settings.</span>
                </div>
                <div class="flex items-center gap-4 text-sm mt-4">
                    <span :class="['bg-custom-500 ring-1 ring-custom-500 ring-offset-2 text-white flex items-center justify-center px-2 py-0.5 rounded-full']">
                       3
                    </span>
                    <span class="text-gray-500">Access the "Billing" section.</span>
                </div>
                <div class="flex items-center gap-4 text-sm mt-4">
                    <span :class="['bg-custom-500 ring-1 ring-custom-500 ring-offset-2 text-white flex items-center justify-center px-2 py-0.5 rounded-full']">
                       4
                    </span>
                    <span class="text-gray-500">Click on the "Add on" tab.</span>
                </div>
                <div class="flex items-center gap-4 text-sm mt-4">
                    <span :class="['bg-custom-500 ring-1 ring-custom-500 ring-offset-2 text-white flex items-center justify-center px-2 py-0.5 rounded-full']">
                       5
                    </span>
                    <span class="text-gray-500">Find your license key listed.</span>
                </div>
                <div class="mt-5">
                    <label for="license_key" class="block text-tiny text-neutral-600 font-medium">
                        License Key
                    </label>
                    <div class="mt-1.5">
                        <div class="relative">
                            <input
                                id="license_key"
                                name="license_key"
                                :type="showKey ? 'text' : 'password'"
                                placeholder="Enter License Key"
                                v-model="license_key" 
                                :class="{'tracking-widest': !showKey}"
                                class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                            />
                            <PasswordVisibility :showPassword="showKey" @toggle="showKey = !showKey"/>
                        </div>
                        <small id="license_key_message" class="error_message text-red-500"></small>
                    </div>
                </div>
                <div class="flex flex-wrap justify-end gap-3 mt-6">
                    <router-link class="text-custom-500 border border-custom-500 rounded-md px-3 py-1.5 text-sm font-medium flex items-center" :to="{
                        name: 'setupDatabase'
                    }">
                        <span class="material-symbols-outlined -ml-1.5"> chevron_left</span>
                        Back
                    </router-link>
                    <Button :disabled="processing">
                        <i v-if="processing" class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"></i>
                        {{processing ? 'Please Wait' : 'Verify'}}
                    </Button>
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
                domain: window.location.host,
                license_key: '',
                processing: false,
                showKey: false
            }
        },
        created(){
            if(!this.databaseComplete){
                this.$router.push({
                    name: 'setupDatabase'
                })
            }
        },
        components:{
            InstallationInfo: defineAsyncComponent(() => import('@/components/InstallationInfo.vue'))
        },
        computed:{
            ...mapState(useSetupStore, ['databaseComplete'])
        },
        methods: {
            async saveKeys(){
                this.hideError();
                this.processing = true

                await this.$axios.post("/setup/verify", {
                    domain: this.domain,
                    license_key: this.license_key
                }).then(({ data }) => {
                    this.$toast.success(data.message);
                    this.$router.push({
                        name: 'setupSmtp'
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
