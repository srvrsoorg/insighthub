<template>
    <div class="relative flex min-h-screen h-full flex-col bg-[#F0EFF3]">
        <!-- Mobile Menu Button -->
        <div class="w-full px-5 visible lg:hidden">
            <button @click="toggleMenu()" class="w-fit flex items-center px-2 py-1.5 rounded-md text-white mt-3 bg-custom-500">
                <span class="material-symbols-outlined">
                    menu
                </span>
            </button>
        </div>
        <div class="flex flex-1 flex-col h-full">
            <!-- Sidebar -->
            <div 
                :class="isOpen ? 'visible translate-x-0' : 'invisible -translate-x-full'"
                class="fixed h-full inset-y-0 left-0 top-0 flex z-50 transition duration-200 ease-in-out lg:visible lg:left-auto lg:mt-0 lg:translate-x-0 transform">
                <Sidenav :sidebarOpen="isOpen" :showNarrowSidebar="showNarrowSidebar" @toggleMenu="toggleMenu()"/>
            </div>

            <!-- Main wrapper -->
            <div class="w-full h-full flex flex-col flex-1" :class="showNarrowSidebar ? 'lg:pl-80' : 'lg:pl-64'">
                <div class="w-full lg:px-8 px-5 pb-5 mb-8 lg:mt-10 mt-5 h-full flex-1">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Sidenav from '@/components/user/Sidenav.vue'
export default{
    data(){
        return{
            isOpen: true, // Flag to control mobile menu visibility
            showNarrowSidebar: false //Flag to show narrow sidebar
        }
    },
    components: {
        Sidenav
    },
    watch: {
        '$route'(val) {
            this.showNarrowSidebar = val.meta.showNarrowSidebar || false;
            if (window.screen.width <= 1023 && this.isOpen) {
                this.toggleMenu()
            }
        }
    },
    created(){
        this.initializeSidebar()
        window.addEventListener("resize", this.initializeSidebar);
        this.showNarrowSidebar = this.$route.meta.showNarrowSidebar || false;
    },
    onBeforeUnmount() {
        window.removeEventListener("resize", this.initializeSidebar);
    },
    methods: {
        // Toggle mobile menu visibility
        toggleMenu() {
            this.isOpen = !this.isOpen;
        },
        
        // Reset mobile menu visibility based on window size
        initializeSidebar() {
            this.isOpen = window.screen.width > 1023;
        }
    }
}
</script>
