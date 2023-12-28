<template>
    <div class="flex h-full min-h-screen bg-gray-100 flex-1 flex-col justify-center py-12 px-8">
        <div class="w-full max-w-5xl mx-auto bg-white sm:px-8 px-6 py-5 shadow rounded-lg">
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
                Final Setup
            </h2>
            <p class="text-sm text-gray-500 mt-2">Tailor InsightHub's look to your brand. Customize appearance effortlessly for a unique monitoring experience.</p>
            <div class="grid md:grid-cols-3 grid-cols-1 gap-6 mt-5">
                <InstallationInfo />
                <form class="md:col-span-2 col-span-1" action="javascript:void(0)" @submit="saveSiteSettings()">
                    <div class="grid md:grid-cols-3 gap-x-5">
                        <div class="md:col-span-2">
                            <label for="app_name" class="block text-tiny text-neutral-600 font-medium">
                                Application Name
                            </label>
                            <div class="mt-1.5">
                                <input
                                    id="app_name"
                                    name="app_name"
                                    type="text"
                                    placeholder="Enter Application Name"
                                    v-model="payload.app_name" 
                                    class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                                />
                                <small id="app_name_message" class="error_message text-red-500"></small>
                            </div>
                        </div>
                        <div class="md:mt-0 mt-5">
                            <label for="color_code" class="block text-tiny text-neutral-600 font-medium">
                                Select Your Brand Color
                            </label>
                            <div class="mt-1.5">
                                <div class="rounded-md border border-neutral-300 focus:border-neutral-300 px-1.5">
                                    <input
                                        id="color_code color_picker"
                                        name="color_code"
                                        type="color"
                                        v-model="payload.color_code" 
                                        class="block appearance-none w-16 rounded-md h-9 bg-transparent"
                                    />
                                </div>
                                <small id="color_code_message" class="error_message text-red-500"></small>
                            </div>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-5 mt-4">
                        <div>
                            <label for="app_name" class="block text-tiny text-neutral-600 font-medium">Favicon</label>
                            <input 
                                type="file" 
                                ref="favicon" 
                                style="display:none;" 
                                accept=".png, .jpg, .jpeg, .ico" 
                                id="favicon" 
                                @change="handleFaviconUpload" 
                            />
                            <div :class="favicon ? 'py-5' : 'py-8'" class="mt-2 px-5 bg-custom-50 border border-dashed border-custom-500 rounded-md flex flex-col items-center justify-center">
                                <div class="overflow-hidden flex flex-col justify-center items-center" v-if="favicon !== null">
                                    <img :src="favicon" class="h-10" />
                                    <div class="mt-1.5" v-if="payload.favicon && payload.favicon.name">
                                        <span class="text-sm">{{ payload.favicon.name }}</span>
                                        <button type="button" @click="payload.favicon = null; favicon = null" class="text-red-500"> <i class="fa fa-times-circle text-xs ml-1"></i></button>
                                    </div>
                                </div>
                                <span class="material-symbols-outlined text-custom-500 text-4xl" v-else>upload</span>
                            </div>
                            <div class="w-full text-center mt-3">
                                <Button type="button" @click="$refs.favicon.click(); favicon = null" >Choose File</Button>
                            </div>
                            <small id="favicon_message" class="error_message text-red-500"></small>
                        </div>
                        <div>
                            <label for="app_name" class="block text-tiny text-neutral-600 font-medium">Brand Logo</label>
                            <input 
                                type="file" 
                                ref="logo" 
                                style="display:none;" 
                                accept=".png, .jpg, .jpeg" 
                                id="logo" 
                                @change="handleSiteLogoUpload" 
                            />
                            <div :class="logo ? 'py-5' : 'py-8'" class="mt-2 px-5 bg-custom-50 border border-dashed border-custom-500 rounded-md flex flex-col items-center justify-center">
                                <div class="overflow-hidden flex flex-col justify-center items-center" v-if="logo !== null">
                                    <img :src="logo" class="h-10" />
                                    <div class="mt-1.5" v-if="payload.logo && payload.logo.name">
                                        <span class="text-sm">{{ payload.logo.name }}</span>
                                        <button type="button" @click="payload.logo = null; logo = null" class="text-red-500"> <i class="fa fa-times-circle text-xs ml-1"></i></button>
                                    </div>
                                </div>
                                <span class="material-symbols-outlined text-custom-500 text-4xl" v-else>upload</span>
                            </div>
                            <div class="w-full text-center mt-3">
                                <Button type="button" @click="$refs.logo.click(); logo = null" >Choose File</Button>
                            </div>
                            <small id="logo_message" class="error_message text-red-500"></small>
                        </div>
                        <div>
                            <label for="app_name" class="block text-tiny text-neutral-600 font-medium">Logo Icon</label>
                            <input 
                                type="file" 
                                ref="icon" 
                                style="display:none;" 
                                accept=".png, .jpg, .jpeg" 
                                id="icon" 
                                @change="handleSmallLogoUpload" 
                            />
                            <div :class="icon ? 'py-5' : 'py-8'" class="mt-2 px-5 bg-custom-50 border border-dashed border-custom-500 rounded-md flex flex-col items-center justify-center">
                                <div class="overflow-hidden flex flex-col justify-center items-center" v-if="icon !== null">
                                    <img :src="icon" class="h-10 w-10" />
                                    <div class="mt-1.5" v-if="payload.icon && payload.icon.name">
                                        <span class="text-sm">{{ payload.icon.name }}</span>
                                        <button type="button" @click="payload.icon = null; icon = null" class="text-red-500"> <i class="fa fa-times-circle text-xs ml-1"></i></button>
                                    </div>
                                </div>
                                <span class="material-symbols-outlined text-custom-500 text-4xl" v-else>upload</span>
                            </div>
                            <div class="w-full text-center mt-3">
                                <Button type="button" @click="$refs.icon.click(); icon = null" >Choose File</Button>
                            </div>
                            <small id="icon_message" class="error_message text-red-500"></small>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-2 grid-cols-1 gap-x-5 mt-4">
                        <div>
                            <label for="retention_period" class="block text-tiny text-neutral-600 font-medium">
                                Data Retention Period
                            </label>
                            <div class="mt-1.5">
                                <input
                                    id="retention_period"
                                    name="retention_period"
                                    type="text"
                                    placeholder="30"
                                    v-model="payload.retention_period" 
                                    class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                                />
                                <small id="retention_period_message" class="error_message text-red-500"></small>
                            </div>
                        </div>
                        <div class="md:mt-0 mt-5">
                            <label for="redis_password" class="block text-tiny text-neutral-600 font-medium">
                                Redis Password
                            </label>
                            <div class="mt-1.5">
                                <input
                                    id="redis_password"
                                    name="redis_password"
                                    type="text"
                                    placeholder="trSdf4KJUH78AtygdKLS"
                                    v-model="payload.redis_password" 
                                    class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                                />
                                <small id="redis_password_message" class="error_message text-red-500"></small>
                            </div>
                        </div>
                    </div>
                    <div class="text-end mt-5">
                        <Button type="submit" :disabled="processing">
                            <i v-if="processing" class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"></i>
                            {{processing ? 'Please Wait' : 'Finish'}}
                        </Button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import siteMixin from '@/mixins/siteSettings'
import {mapState} from 'pinia'
import {useSetupStore} from '@/store/setup.js'
import { defineAsyncComponent } from 'vue';

export default{
    mixins: [siteMixin],
    data(){
        return{
            payload: {
                app_name: '',
                logo: null,
                icon: null,
                favicon: null,
                color_code: '020C7E',
                retention_period: '',
                redis_password: null
            },
            favicon: null,
            logo: null,
            icon: null,
            processing: false
        }
    },
    components:{
        InstallationInfo: defineAsyncComponent(() => import('@/components/InstallationInfo.vue'))
    },
    computed:{
        ...mapState(useSetupStore, ['registerComplete'])
    },
    created(){
        if(!this.registerComplete){
            this.$router.push({
                name: 'register'
            })
        }else{
            this.payload.app_name = this.app_name
            this.payload.color_code = `#${this.color_code}`
        }
    },
    methods: {
        handleSiteLogoUpload() {
            this.payload.logo = this.$refs.logo.files[0];
            let reader = new FileReader();
            reader.onload = (e) => {
                this.logo = e.target.result;
            };
            reader.readAsDataURL(this.$refs.logo.files[0])
            document.getElementById('logo').value = ''
        },
        handleSmallLogoUpload() {
            this.payload.icon = this.$refs.icon.files[0];
            let reader = new FileReader();
            reader.onload = (e) => {
                this.icon = e.target.result;
            };
            reader.readAsDataURL(this.$refs.icon.files[0])
            document.getElementById('icon').value = ''
        },
        handleFaviconUpload(e) {
            this.payload.favicon = this.$refs.favicon.files[0];
            let reader = new FileReader();
            reader.onload = (e) => {
                this.favicon = e.target.result;
            };
            reader.readAsDataURL(this.$refs.favicon.files[0])
            document.getElementById('favicon').value = ''
        }
    }
}
</script>