<template>
    <div class="grid xl:grid-cols-3 grid-cols-1 gap-5 mt-5">
        <div class="bg-white rounded-lg text-tiny px-4 py-3">
            <div class="bg-gray-50" v-if="application">
                <div class="flex justify-between items-center gap-5 px-3.5 py-2 border-b border-slate-200">
                    <span>Application</span>
                    <span class="text-gray-500 truncate" v-tooltip="application.name">{{ application.name }}</span>
                </div>
                <div class="flex justify-between items-center gap-5 px-3.5 py-2 border-b border-slate-200">
                    <span>Framework</span>
                    <span class="text-gray-500 capitalize">{{ application.framework }}</span>
                </div>
                <div class="flex justify-between items-center gap-5 px-3.5 py-2 border-b border-slate-200">
                    <span>Domain</span>
                    <span class="text-gray-500 truncate" v-tooltip="application.primary_domain">{{ application.primary_domain }}</span>
                </div>
                <div class="flex justify-between items-center gap-5 px-3.5 py-2 border-b border-slate-200">
                    <span>PHP Version</span>
                    <span class="text-gray-500" v-if="application.php_version == 8">PHP 8.0</span>
                    <span class="text-gray-500" v-else-if="application.php_version == 7">PHP 7.0</span>
                    <span class="text-gray-500" v-else>PHP {{ application.php_version }}</span>
                </div>
                <div class="flex justify-between items-center gap-5 px-3.5 py-2 border-b border-slate-200">
                    <span>Size</span>
                    <span class="text-gray-500">{{ application.size }} MB</span>
                </div>
                <div class="flex justify-between items-center gap-5 px-3.5 py-2 border-b border-slate-200">
                    <span>SSL</span>
                    <span class="text-green-500 text-sm" v-if="application.ssl">
                        Installed
                    </span>
                    <span class="text-red-500 text-sm" v-else>
                        Not Installed
                    </span>
                </div>
                <div class="flex justify-between items-center gap-5 px-3.5 py-2">
                    <span>Status</span>
                    <span 
                        class="px-3 py-1 flex items-center gap-1.5 rounded-2xl text-xs"
                        :class="application.active ? 'text-green-600 bg-green-200' : 'text-red-500 bg-red-100'"
                    >
                        <i class="fas fa-circle"></i>
                        {{ application.active ? 'Active' : 'Not Active' }}
                    </span>
                </div>
            </div>
            <div class="bg-gray-50" v-else>
                <div class="px-5 py-2 border-b border-slate-200" v-for="i in 5" :key="i">
                    <Skeleton :count="1" />
                </div>
            </div>
        </div>
        <div class="xl:col-span-2 grid xl:grid-cols-3 md:grid-cols-3 gap-5" v-if="overview">
            <div :class="[isLightColor ? 'border-custom-600' : 'border-custom-500','bg-white rounded-lg border-t-4 flex flex-col justify-center items-center px-4 py-1.5']">
                <span :class="[isLightColor ? 'text-custom-600' : 'text-custom-500', 'material-symbols-outlined bg-gray-100 rounded-full px-2 py-1 w-fit text-2xl']">
                    description
                </span>
                <span class="text-center my-2">Total Request</span>
                <span class="text-xl text-gray-500">{{ overview.total_requests ?? '-' }}</span>
            </div>
            <div :class="[isLightColor ? 'border-custom-600' : 'border-custom-500','bg-white rounded-lg border-t-4 flex flex-col justify-center items-center px-4 py-1.5']">
                <span :class="[isLightColor ? 'text-custom-600' : 'text-custom-500', 'material-symbols-outlined bg-gray-100 rounded-full px-2 py-1 w-fit text-2xl']">
                    dangerous
                </span>
                <span class="text-center my-2">Failed Request</span>
                <span class="text-xl text-gray-500">{{ overview.failed_requests ?? '-' }}</span>
            </div>
            <div :class="[isLightColor ? 'border-custom-600' : 'border-custom-500','bg-white rounded-lg border-t-4 flex flex-col justify-center items-center px-4 py-1.5']">
                <span :class="[isLightColor ? 'text-custom-600' : 'text-custom-500', 'material-symbols-outlined bg-gray-100 rounded-full px-2 py-1 w-fit text-2xl']">
                    browser_updated
                </span>
                <span class="text-center my-3">Top Browser Agent</span>
                <template v-if="overview.top_browser_agent">
                    <span class="text-xl text-gray-500">
                        {{ overview.top_browser_agent.browser == '0' ? 'Other' : overview.top_browser_agent.browser }}
                    </span>
                </template>
                <span class="text-xl text-gray-500" v-else>-</span>
            </div>
            <div :class="[isLightColor ? 'border-custom-600' : 'border-custom-500','bg-white rounded-lg border-t-4 flex flex-col justify-center items-center px-4 py-1.5']">
                <span :class="[isLightColor ? 'text-custom-600' : 'text-custom-500', 'material-symbols-outlined bg-gray-100 rounded-full px-2 py-1 w-fit text-2xl']">
                    laptop_mac
                </span>
                <span class="text-center my-2">Most Used Device</span>
                <span class="text-xl text-gray-500">{{ overview.most_used_device ? overview.most_used_device.device : '-' }}</span>
            </div>
            <div :class="[isLightColor ? 'border-custom-600' : 'border-custom-500','bg-white rounded-lg border-t-4 flex flex-col justify-center items-center px-4 py-1.5']">
                <span :class="[isLightColor ? 'text-custom-600' : 'text-custom-500', 'material-symbols-outlined bg-gray-100 rounded-full px-2 py-1 w-fit text-2xl']">
                    stacks
                </span>
                <span class="text-center my-2">Most Used Platform</span>
                <template v-if="overview.most_used_platform">
                    <span class="text-xl text-gray-500">
                        {{ overview.most_used_platform.platform == '0' ? 'Other' : overview.most_used_platform.platform }}
                    </span>
                </template>
                <span class="text-xl text-gray-500" v-else>-</span>
            </div>
            <div :class="[isLightColor ? 'border-custom-600' : 'border-custom-500','bg-white rounded-lg border-t-4 flex flex-col justify-center items-center px-4 py-1.5']">
                <span :class="[isLightColor ? 'text-custom-600' : 'text-custom-500', 'material-symbols-outlined bg-gray-100 rounded-full px-2 py-1 w-fit text-3xl']">
                    http
                </span>
                <span class="text-center mb-2">Most Common Protocol</span>
                <span class="text-xl text-gray-500">{{ overview.most_common_protocol ? overview.most_common_protocol.protocol : '-' }}</span>
            </div>
        </div>
        <div class="xl:col-span-2 grid xl:grid-cols-3 md:grid-cols-3 gap-5" v-else>
            <div class="bg-white rounded-lg text-tiny px-4 py-3" v-for="i in 6" :key="i">
                <Skeleton :count="4" />
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['application', 'overview']
}
</script>