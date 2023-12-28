<template>
    <div class="grid xl:grid-cols-5 md:grid-cols-3 sm:grid-cols-2 grid-cols-1 gap-5 mt-5">
        <div :class="[isLightColor ? 'border-custom-600' : 'border-custom-500', 'bg-white px-4 py-3 rounded-sm border-b-4']">
            <div class="flex justify-between items-center">
                <div class="flex flex-col gap-1.5">
                    <span>1xx Informational</span>
                    <span class="text-2xl font-cera-pro-light text-gray-500">{{ requestCount.information }}</span>
                </div>
                <div :class="[isLightColor ? 'bg-custom-500' : 'bg-custom-500','h-fit rounded-full px-2 py-1 flex items-center']">
                    <span :class="[textColorClass, 'material-symbols-outlined text-xl']">
                        info
                    </span>
                </div>
            </div>
        </div>
        <div :class="[isLightColor ? 'border-custom-600' : 'border-custom-500', 'bg-white px-4 py-3 rounded-sm border-b-4']">
            <div class="flex justify-between items-center">
                <div class="flex flex-col gap-1.5">
                    <span>2xx Successful</span>
                    <span class="text-2xl font-cera-pro-light text-gray-500">{{ requestCount.success }}</span>
                </div>
                <div :class="[isLightColor ? 'bg-custom-500' : 'bg-custom-500','h-fit rounded-full px-2 py-1 flex items-center']">
                    <span :class="[textColorClass, 'material-symbols-outlined text-xl']">
                        check_circle
                    </span>
                </div>
            </div>
        </div>
        <div :class="[isLightColor ? 'border-custom-600' : 'border-custom-500', 'bg-white px-4 py-3 rounded-sm border-b-4']">
            <div class="flex justify-between items-center">
                <div class="flex flex-col gap-1.5">
                    <span>3xx Redirection</span>
                    <span class="text-2xl font-cera-pro-light text-gray-500">{{ requestCount.redirection }}</span>
                </div>
                <div :class="[isLightColor ? 'bg-custom-500' : 'bg-custom-500','h-fit rounded-full px-2 py-1 flex items-center']">
                    <span :class="[textColorClass, 'material-symbols-outlined text-xl']">
                        forward
                    </span>
                </div>
            </div>
        </div>
        <div :class="[isLightColor ? 'border-custom-600' : 'border-custom-500', 'bg-white px-4 py-3 rounded-sm border-b-4']">
            <div class="flex justify-between items-center">
                <div class="flex flex-col gap-1.5">
                    <span>4xx Client Error</span>
                    <span class="text-2xl font-cera-pro-light text-gray-500">{{ requestCount.notFound }}</span>
                </div>
                <div :class="[isLightColor ? 'bg-custom-500' : 'bg-custom-500','h-fit rounded-full px-2 py-1 flex items-center']">
                    <span :class="[textColorClass, 'material-symbols-outlined text-xl']">
                        dangerous
                    </span>
                </div>
            </div>
        </div>
        <div :class="[isLightColor ? 'border-custom-600' : 'border-custom-500', 'bg-white px-4 py-3 rounded-sm border-b-4']">
            <div class="flex justify-between items-center">
                <div class="flex flex-col gap-1.5">
                    <span>5xx Server Error</span>
                    <span class="text-2xl font-cera-pro-light text-gray-500">{{ requestCount.failed }}</span>
                </div>
                <div :class="[isLightColor ? 'bg-custom-500' : 'bg-custom-500','h-fit rounded-full px-2 py-1 flex items-center']">
                    <span :class="[textColorClass, 'material-symbols-outlined text-xl']">
                        error
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['requestCount'],
}
</script>