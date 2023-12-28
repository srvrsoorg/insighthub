<template>
    <div class="container-fluid">
        <div class="grid xl:grid-cols-3 md:grid-cols-3 grid-cols-1 mt-5 gap-5">
            <div :class="['bg-neutral-50 rounded-lg px-5 py-3']">
                <div class="flex justify-between items-center h-full" v-if="server">
                    <span>{{server.name}}</span>
                    <span :class="[textColorClass, 'material-symbols-outlined text-xl bg-custom-500 px-2 py-1 rounded-full']">
                        dns
                    </span>
                </div>
                <Skeleton v-else :count="2"/>
            </div>
            <div :class="['bg-neutral-50 rounded-lg px-5 py-3']">
                <div class="flex justify-between items-center h-full" v-if="server">
                    <span class="flex items-center gap-2">
                        {{server.ip}}
                        <button @click="copyToClipboard(server.ip)">
                            <span class="material-symbols-outlined text-lg text-blue-500">
                                content_copy
                            </span>
                        </button>
                    </span>
                    <span :class="[textColorClass, 'material-symbols-outlined text-xl bg-custom-500 px-2 py-1 rounded-full']">
                        location_on
                    </span>
                </div>
                <Skeleton v-else :count="2"/>
            </div>
            <div :class="['bg-neutral-50 rounded-lg px-5 py-3']">
                <div class="flex justify-between items-center h-full" v-if="server">
                    <span>Status</span>
                    <span 
                        class="px-3 py-1.5 flex items-center gap-1.5 rounded-2xl text-xs"
                        :class="server.agent_status == '1' ? 'text-green-600 bg-green-100' : 'text-red-500 bg-red-100'"
                    >
                        <i class="fas fa-circle"></i>
                        {{ server.agent_status == '1' ? 'Connected' : 'Not Connected' }}
                    </span>
                </div>
                <Skeleton v-else :count="2"/>
            </div>
        </div>
    </div>
</template>

<script>
export default{
    props: ['server']
}
</script>
