<template>
    <div class="no-scrollbar h-full w-64 sticky top-0 bg-white">
        <CustomScrollbar class="h-full">
            <!-- Sidebar for Tablet and Mobile Screen -->
            <TransitionRoot as="template" :show="sidebarOpen">
                <Dialog as="div" class="relative z-50 lg:hidden" :open="sidebarOpen" @close="closeMenu()">
                    <TransitionChild as="template" enter="transition-opacity ease-linear duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="transition-opacity ease-linear duration-300" leave-from="opacity-100" leave-to="opacity-0">
                        <div class="fixed inset-0 bg-gray-900/40" />
                    </TransitionChild>
                    <div class="fixed inset-0 flex">
                        <TransitionChild as="template" enter="transition ease-in-out duration-300 transform" enter-from="-translate-x-full" enter-to="translate-x-0" leave="transition ease-in-out duration-300 transform" leave-from="translate-x-0" leave-to="-translate-x-full">
                            <DialogPanel class="relative mr-16 w-64 max-w-[256px] flex flex-1">
                                <TransitionChild as="template" enter="ease-in-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in-out duration-300" leave-from="opacity-100" leave-to="opacity-0">
                                    <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                                    <button type="button" class="-m-2.5 p-2.5" @click="$emit('toggleMenu')">
                                        <span class="sr-only">Close sidebar</span>
                                        <span class="material-symbols-outlined text-white">
                                            close
                                        </span>
                                    </button>
                                    </div>
                                </TransitionChild>
                                <div class="flex flex-grow h-full">
                                    <SidebarItems :menuList="menuList"/>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </Dialog>
            </TransitionRoot>
    
            <!-- Sidebar for Desktop Screen -->
            <div class="flex flex-grow h-full min-h-full">
                <SidebarItems :menuList="menuList"/>
            </div>
        </CustomScrollbar>
    </div>
</template>

<script>
import sideMenu from '@/sideMenu/admin'
import { mapState } from 'pinia'
import { useAuthStore } from '@/store/auth'
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue'
import SidebarItems from '@/components/admin/SidebarItems.vue'

export default{
    props: ['sidebarOpen'],
    data(){
        return{
            menuList: []
        }
    },
    components: {
        Dialog, DialogPanel, TransitionChild, TransitionRoot,
        SidebarItems
    },
    watch: {
        '$route':{
            handler(val){
                if (window.screen.width <= 1023) {
                    this.$emit('toggleMenu')
                }
            }
        }
    },
    created(){
        this.menuList = sideMenu
    },
    computed: {
        ...mapState(useAuthStore, ['user'])
    },
    methods: {
        closeMenu(){
            this.$emit('toggleMenu')
        }
    }
}
</script>
