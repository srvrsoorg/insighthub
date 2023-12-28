<template>
    <div class="grid xl:grid-cols-5 md:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-5 mt-5">
        <div :class="['bg-white rounded-lg']">
            <template v-if="requestInsights && !requestInsightsRefreshing">
                <div class="px-3 py-2 text-center">
                    <span :class="[isLightColor ? 'text-custom-600' : 'text-custom-500', 'text-base']">IP</span>
                </div>
                <hr />
                <div class="flex justify-between items-center px-3 py-1.5 -mt-6">
                    <span :class="[textColorClass, 'material-symbols-outlined text-xl bg-custom-500 px-1.5 py-0.5 rounded-full']">
                        location_on
                    </span>
                </div>
                <div class="px-3 pt-1 pb-2">
                    <div class="flex justify-between gap-3 mb-0.5">
                        <span v-tooltip="requestInsights.Ip && requestInsights.Ip.ip ? requestInsights.Ip.ip : '-'" class="text-sm text-gray-500 truncate max-w-full">
                            {{ requestInsights.Ip && requestInsights.Ip.ip ? requestInsights.Ip.ip : '-' }}
                        </span>
                        <span class="text-sm text-gray-900">
                            {{ requestInsights.Ip && requestInsights.Ip.count ? requestInsights.Ip.count : '-' }}
                        </span>
                    </div>
                    <router-link v-if="requestInsights.Ip && !$route.params.application" :to="{
                        name: 'applicationDashboard',
                        params: {
                            server: $route.params.server,
                            application: requestInsights.Ip.application_id 
                        }
                    }" :class="[isLightColor ? 'text-custom-600' : 'text-custom-500', 'text-sm']">
                        <label class="hover:underline cursor-pointer">{{ requestInsights.Ip.application_name }}</label>
                    </router-link> 
                </div>
            </template>
            <div class="p-3" v-else>
                    <Skeleton :count="3" :bgColor="'bg-gray-300'"/>
            </div>
        </div>
        <div :class="['bg-white rounded-lg']">
            <template v-if="requestInsights && !requestInsightsRefreshing">
                <div class="px-3 py-2 text-center">
                    <span :class="[isLightColor ? 'text-custom-600' : 'text-custom-500', 'text-base']">Status</span>
                </div>
                <hr />
                <div class="flex justify-between items-center px-3 py-1.5 -mt-6">
                    <span :class="[textColorClass, 'material-symbols-outlined text-xl bg-custom-500 px-1.5 py-0.5 rounded-full']">
                        code
                    </span>
                </div>
                <div class="px-3 pt-1 pb-2">
                    <div class="flex justify-between gap-3 mb-0.5">
                        <span class="text-sm text-gray-500 truncate max-w-full">
                            {{  requestInsights.Status && requestInsights.Status.status ? requestInsights.Status.status : '-' }}
                        </span>
                        <span class="text-sm text-gray-900">
                            {{ requestInsights.Status && requestInsights.Status.count ? requestInsights.Status.count: '-' }}
                        </span>
                    </div>
                    <router-link v-if="requestInsights.Status && !$route.params.application" :to="{
                        name: 'applicationDashboard',
                        params: {
                            server: $route.params.server,
                            application: requestInsights.Status.application_id 
                        }
                    }" :class="[isLightColor ? 'text-custom-600' : 'text-custom-500', 'text-sm']">
                        <label class="hover:underline cursor-pointer">{{ requestInsights.Status.application_name }}</label>
                    </router-link> 
                </div>
            </template>
            <div class="p-3" v-else>
                    <Skeleton :count="3" :bgColor="'bg-gray-300'"/>
            </div>
        </div>
        <div :class="['bg-white rounded-lg']">
            <template v-if="requestInsights && !requestInsightsRefreshing">
                <div class="px-3 py-2 text-center">
                    <span :class="[isLightColor ? 'text-custom-600' : 'text-custom-500', 'text-base']">URL</span>
                </div>
                <hr />
                <div class="flex justify-between items-center px-3 py-1.5 -mt-6">
                    <span :class="[textColorClass, 'material-symbols-outlined text-xl bg-custom-500 px-1.5 py-0.5 rounded-full']">
                        link
                    </span>
                </div>
                <div class="px-3 pt-1 pb-2">
                    <div class="flex justify-between gap-3 mb-0.5">
                        <span v-tooltip="requestInsights.Url && requestInsights.Url.url ? requestInsights.Url.url : '-'" class="text-sm text-gray-500 truncate max-w-full">
                            {{ requestInsights.Url && requestInsights.Url.url ? requestInsights.Url.url : '-' }}
                        </span>
                        <span class="text-sm text-gray-900">
                            {{ requestInsights.Url && requestInsights.Url.count ? requestInsights.Url.count : '-' }}
                        </span>
                    </div>
                    <router-link v-if="requestInsights.Url && !$route.params.application" :to="{
                        name: 'applicationDashboard',
                        params: {
                            server: $route.params.server,
                            application: requestInsights.Url.application_id 
                        }
                    }" :class="[isLightColor ? 'text-custom-600' : 'text-custom-500', 'text-sm']">
                        <label class="hover:underline cursor-pointer">{{ requestInsights.Url.application_name }}</label>
                    </router-link> 
                </div>
            </template>
            <div class="p-3" v-else>
                    <Skeleton :count="3" :bgColor="'bg-gray-300'"/>
            </div>
        </div>
        <div :class="['bg-white rounded-lg']">
            <template v-if="requestInsights && !requestInsightsRefreshing">
                <div class="px-3 py-2 text-center">
                    <span :class="[isLightColor ? 'text-custom-600' : 'text-custom-500', 'text-base']">Referrer</span>
                </div>
                <hr />
                <div class="flex justify-between items-center px-3 py-1.5 -mt-6">
                    <span :class="[textColorClass, 'material-symbols-outlined text-xl bg-custom-500 px-1.5 py-0.5 rounded-full']">
                        public
                    </span>
                </div>
                <div class="px-3 pt-1 pb-2">
                    <div class="flex justify-between gap-3 mb-0.5">
                        <span v-tooltip="requestInsights.Referrer && requestInsights.Referrer.referrer_url ? requestInsights.Referrer.referrer_url : '-'" class="text-sm text-gray-500 truncate max-w-full">
                            {{ requestInsights.Referrer && requestInsights.Referrer.referrer_url ? requestInsights.Referrer.referrer_url : '-' }}
                        </span>
                        <span class="text-sm text-gray-900">
                            {{ requestInsights.Referrer && requestInsights.Referrer.count ? requestInsights.Referrer.count : '-' }}
                        </span>
                    </div>
                    <router-link v-if="requestInsights.Referrer && !$route.params.application" :to="{
                        name: 'applicationDashboard',
                        params: {
                            server: $route.params.server,
                            application: requestInsights.Referrer.application_id 
                        }
                    }" :class="[isLightColor ? 'text-custom-600' : 'text-custom-500', 'text-sm']">
                        <label class="hover:underline cursor-pointer">{{ requestInsights.Referrer.application_name }}</label>
                    </router-link> 
                </div>
            </template>
            <div class="p-3" v-else>
                    <Skeleton :count="3" :bgColor="'bg-gray-300'"/>
            </div>
        </div>
        <div :class="['bg-white rounded-lg']">
            <template v-if="requestInsights && !requestInsightsRefreshing">
                <div class="px-3 py-2 text-center">
                    <span :class="[isLightColor ? 'text-custom-600' : 'text-custom-500', 'text-base']">Browser</span>
                </div>
                <hr />
                <div class="flex justify-between items-center px-3 py-1.5 -mt-6">
                    <span :class="[textColorClass, 'material-symbols-outlined text-xl bg-custom-500 px-1.5 py-0.5 rounded-full']">
                        language
                    </span>
                </div>
                <div class="px-3 pt-1 pb-2">
                    <div class="flex justify-between gap-3 mb-0.5">
                        <span v-if="requestInsights.Browser" v-tooltip="requestInsights.Browser.browser == '0' ? 'Other' : requestInsights.Browser.browser" class="text-sm text-gray-500 truncate max-w-full">
                            {{ requestInsights.Browser.browser == '0' ? 'Other' : requestInsights.Browser.browser }}
                        </span>
                        <span class="text-gray-500 truncate max-w-full" v-else>-</span>
                        <span class="text-sm text-gray-900">
                            {{ requestInsights.Browser && requestInsights.Browser.count ? requestInsights.Browser.count : '-' }}
                        </span>
                    </div>
                    <router-link v-if="requestInsights.Browser && !$route.params.application" :to="{
                        name: 'applicationDashboard',
                        params: {
                            server: $route.params.server,
                            application: requestInsights.Browser.application_id 
                        }
                    }" :class="[isLightColor ? 'text-custom-600' : 'text-custom-500', 'text-sm']">
                        <label class="hover:underline cursor-pointer">{{ requestInsights.Browser.application_name }}</label>
                    </router-link> 
                </div>
            </template>
            <div class="p-3" v-else>
                    <Skeleton :count="3" :bgColor="'bg-gray-300'"/>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['requestInsights'],
    data(){
        return{
            requestInsightsRefreshing: false,
        }
    }
}
</script>