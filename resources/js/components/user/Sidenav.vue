<template>
    <div class="no-scrollbar h-full bg-white" :class="showNarrowSidebar ? 'w-80' : 'w-64'">
        <!-- Sidebar for Tablet and Mobile Screen -->
        <TransitionRoot as="template" :show="sidebarOpen">
            <Dialog as="div" class="relative z-50 lg:hidden" :open="sidebarOpen" @close="$emit('toggleMenu')">
                <TransitionChild as="template" enter="transition-opacity ease-linear duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="transition-opacity ease-linear duration-300" leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 bg-gray-900/40" />
                </TransitionChild>
                <div class="fixed inset-0 flex">
                    <TransitionChild as="template" enter="transition ease-in-out duration-300 transform" enter-from="-translate-x-full" enter-to="translate-x-0" leave="transition ease-in-out duration-300 transform" leave-from="translate-x-0" leave-to="-translate-x-full">
                        <DialogPanel class="relative mr-16 flex flex-1" :class="showNarrowSidebar ? 'w-80 max-w-[320px]' : 'w-64 max-w-[256px]'">
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
                                <NarrowSidebar :menuList="narrowSidebarLinks" v-if="showNarrowSidebar"/>
                                <SidebarItems :menuList="menuList" :showNarrowSidebar="showNarrowSidebar"/>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </Dialog>
        </TransitionRoot>

        <!-- Sidebar for Desktop Screen -->
        <div class="flex flex-grow h-full min-h-full">
            <NarrowSidebar :menuList="narrowSidebarLinks" v-if="showNarrowSidebar"/>
            <SidebarItems :menuList="menuList" :showNarrowSidebar="showNarrowSidebar"/>
        </div>
    </div>
</template>

<script>
import sideMenu from '@/sideMenu/dashboard'
import serverSideMenu from '@/sideMenu/server'
import appSideMenu from '@/sideMenu/application'
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue'
import SidebarItems from '@/components/user/SidebarItems.vue'
import NarrowSidebar from '@/components/user/NarrowSidebar.vue'

export default{
    props: ['sidebarOpen', 'showNarrowSidebar'],
    data(){
        return{
            menuList: [],
            narrowSidebarLinks: []
        }
    },
    components: {
        Dialog, DialogPanel, TransitionChild, TransitionRoot, SidebarItems, NarrowSidebar
    },
    watch: {
        '$route'() {
            this.changeMenuLinks();
        }
    },
    created(){
        this.menuList = sideMenu
        this.changeMenuLinks()
    },
    methods: {
        // Change menu links based on the route and sidebar visibility
        changeMenuLinks(){
            if(this.$route.meta.showNarrowSidebar){
                this.narrowSidebarLinks = sideMenu
                const replaceServerAppId = (link) => {
                    const updatedLink = {
                        ...link,
                        uri: this.updateUri(link.uri)
                    };

                    // If the current link has children, recursively update their URIs
                    if (updatedLink.children && updatedLink.children.length > 0) {
                        updatedLink.children = updatedLink.children.map(replaceServerAppId);
                    }

                    return updatedLink;
                };

                 // Update the parent and children URIs
                 if(this.$route.params.application && this.$route.params.server){
                    this.menuList = appSideMenu.map(replaceServerAppId)
                }else{
                    this.menuList = serverSideMenu.map(replaceServerAppId);
                }
            }else{
                this.menuList = sideMenu
            }
        },
        updateUri(uri) {
            return uri
                .replace(/{server}/g, this.$route.params.server)
                .replace('{application}', this.$route.params.application);
        }
    }
}
</script>
