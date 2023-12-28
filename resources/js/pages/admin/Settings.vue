<template>
    <div class="container-fluid">
        <h1 class="text-2xl text-black">Settings</h1>
        <form action="javascript:void(0)" class="2xl:w-1/2 xl:w-2/3 md:w-5/6 w-full space-y-4 bg-white shadow p-5 mt-3 rounded-lg" @submit="saveSiteSettings()">
            <div class="grid md:grid-cols-3 gap-5">
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
                            class="block w-full bg-white rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal text-sm leading-6 focus:ring-0"
                        />
                        <small id="app_name_message" class="error_message text-red-500"></small>
                    </div>
                </div>
                <div>
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
                                class="block appearance-none w-16 h-9 bg-transparent"
                            />
                        </div>
                        <small id="color_code_message" class="error_message text-red-500"></small>
                    </div>
                </div>
            </div>
            <div class="grid sm:grid-cols-3 grid-cols-1 gap-5">
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
                    <div :class="favicon ? 'py-5' : 'py-8'" class="mt-1.5 px-5 bg-blue-100 border border-dashed border-blue-500 rounded-md flex flex-col items-center justify-center">
                        <div class="overflow-hidden flex flex-col justify-center items-center" v-if="favicon !== null">
                            <img :src="favicon" class="h-10" />
                            <div class="mt-1.5" v-if="payload.favicon && payload.favicon.name">
                                <span class="text-sm">{{ payload.favicon.name }}</span>
                                <button type="button" @click="payload.favicon = null; favicon = null" class="text-red-500"> <i class="fa fa-times-circle text-xs ml-1"></i></button>
                            </div>
                        </div>
                        <span class="material-symbols-outlined text-blue-600 text-4xl" v-else>upload</span>
                    </div>
                    <div class="w-full text-center mt-3">
                        <Button type="button" @click="$refs.favicon.click(); favicon = null" >Choose File</Button>
                    </div>
                    <small id="favicon_message" class="error_message text-red-500"></small>
                </div>
                <div>
                    <label for="logo" class="block text-tiny text-neutral-600 font-medium">Brand Logo</label>
                    <input 
                        type="file" 
                        ref="logo" 
                        style="display:none;" 
                        accept=".png, .jpg, .jpeg" 
                        id="logo" 
                        @change="handleSiteLogoUpload" 
                    />
                    <div :class="logo ? 'py-5' : 'py-8'" class="mt-1.5 px-5 bg-blue-100 border border-dashed border-blue-500 rounded-md flex flex-col items-center justify-center">
                        <div class="overflow-hidden flex flex-col justify-center items-center" v-if="logo !== null">
                            <img :src="logo" class="h-10" />
                            <div class="mt-1.5" v-if="payload.logo && payload.logo.name">
                                <span class="text-sm">{{ payload.logo.name }}</span>
                                <button type="button" @click="payload.logo = null; logo = null" class="text-red-500"> <i class="fa fa-times-circle text-xs ml-1"></i></button>
                            </div>
                        </div>
                        <span class="material-symbols-outlined text-blue-600 text-4xl" v-else>upload</span>
                    </div>
                    <div class="w-full text-center mt-3">
                        <Button type="button" @click="$refs.logo.click(); logo = null" >Choose File</Button>
                    </div>
                    <small id="logo_message" class="error_message text-red-500"></small>
                </div>
                <div>
                    <label for="icon" class="block text-tiny text-neutral-600 font-medium">Logo Icon</label>
                    <input 
                        type="file" 
                        ref="icon" 
                        style="display:none;" 
                        accept=".png, .jpg, .jpeg" 
                        id="icon" 
                        @change="handleSmallLogoUpload" 
                    />
                    <div :class="icon ? 'py-5' : 'py-8'" class="mt-1.5 px-5 bg-blue-100 border border-dashed border-blue-500 rounded-md flex flex-col items-center justify-center">
                        <div class="overflow-hidden flex flex-col justify-center items-center" v-if="icon !== null">
                            <img :src="icon" class="h-10 w-10" />
                            <div class="mt-1.5" v-if="payload.icon && payload.icon.name">
                                <span class="text-sm">{{ payload.icon.name }}</span>
                                <button type="button" @click="payload.icon = null; icon = null" class="text-red-500"> <i class="fa fa-times-circle text-xs ml-1"></i></button>
                            </div>
                        </div>
                        <span class="material-symbols-outlined text-blue-600 text-4xl" v-else>upload</span>
                    </div>
                    <div class="w-full text-center mt-3">
                        <Button type="button" @click="$refs.icon.click(); icon = null" >Choose File</Button>
                    </div>
                    <small id="icon_message" class="error_message text-red-500"></small>
                </div>
            </div>
            <div class="grid md:grid-cols-2 gap-5">
                <div>
                    <label for="retention_period" class="block text-tiny text-neutral-600 font-medium">
                        Data Retention Period (In Days)
                    </label>
                    <div class="mt-1.5">
                        <input
                            id="retention_period"
                            name="retention_period"
                            type="text"
                            placeholder="30"
                            v-model="payload.retention_period" 
                            class="block w-full bg-white rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal text-sm leading-6 focus:ring-0"
                        />
                        <small id="retention_period_message" class="error_message text-red-500"></small>
                    </div>
                </div>
                <div>
                    <label for="redis_password" class="block text-tiny text-neutral-600 font-medium">
                        Redis Password
                    </label>
                    <div class="mt-1.5">
                        <input
                            id="redis_password"
                            name="redis_password"
                            type="text"
                            placeholder="fGT65fdfhjhZz"
                            v-model="payload.redis_password" 
                            class="block w-full bg-white rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal text-sm leading-6 focus:ring-0"
                        />
                        <small id="redis_password_message" class="error_message text-red-500"></small>
                    </div>
                </div>
            </div>
            <div>
                <Button type="submit" class="mt-2" :disabled="processing">
                    <i v-if="processing" class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"></i>
                    {{processing ? 'Please Wait' : 'Done'}}
                </Button>
            </div>
        </form>
    </div>
</template>

<script>
import siteMixin from '@/mixins/siteSettings'
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
    created(){
        this.verifyApi(this.fetchSiteDetails)
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
        },
        async fetchSiteDetails(){
            this.$axios.get(`/site-setting`).then(({data}) => {
                if(data){
                    this.payload.app_name = data.app_name
                    this.payload.retention_period = data.retention_period
                    this.payload.redis_password = data.redis_password ? data.redis_password : null
                    this.payload.color_code = `#${data.color_code}`
                    this.favicon = data.favicon ? data.favicon : null
                    this.logo = data.logo ? data.logo : null
                    this.icon = data.icon ? data.icon : null
                }
            }).catch(({ response: { data } }) => {
                this.$toast.error(data.message);
            })
        }
    }
}

</script>