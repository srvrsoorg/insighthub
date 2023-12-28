<template>
    <div class="flow-root h-full">
        <div class="overflow-x-auto w-full">
            <div class="inline-block w-full align-middle">
                <div class="overflow-hidden bg-white pb-1 rounded-lg">
                    <slot name="header"></slot>
                    <div class="px-5">
                        <CustomScrollbar :class="bodyHeight ? bodyHeight : 'h-full'">
                            <table class="min-w-full text-tiny mt-4">
                                <thead class="px-4 bg-[#F4F3F7] border-b border-neutral-300 sticky top-0" v-if="head.length">
                                    <tr>
                                        <td
                                            v-for="(header, index) in head"
                                            :class="[
                                                'px-4 py-3 text-left text-zinc-900',
                                                header && header.classes ? header.classes : ''
                                            ]"
                                            :key="index"
                                        >
                                            <template v-if="header!==null">
                                                <span v-if="header.tooltip" v-tooltip="header.tooltip">
                                                    {{header.title ? header.title : header}}
                                                </span>
                                                <span v-else>
                                                    {{header.title ? header.title : header}}
                                                </span>
                                            </template>
                                            <Skeleton v-else :count="1" />
                                        </td>
                                    </tr>
                                </thead>
                                <tbody class="text-zinc-500 tbody">
                                    <slot />
                                </tbody>
                            </table>
                        </CustomScrollbar>
                        <slot name="pagination"></slot>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default{
    props:{
        head:{
            type: Array,
            default: []
        },
        bodyHeight: {
            type: String
        }   
    }
}
</script>

