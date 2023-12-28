<template>
    <div class="relative flex min-h-screen h-full flex-col bg-[#F0EFF3]">
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
                class="fixed inset-y-0 left-0 top-0 flex z-50 transition duration-200 ease-in-out lg:visible lg:left-auto lg:mt-0 lg:translate-x-0 transform">
                <Sidebar :sidebarOpen="isOpen" @toggleMenu="toggleMenu()"/>
            </div>

            <!-- Main wrapper -->
            <div class="w-full h-full flex flex-col flex-1 lg:pl-64">
                <div class="w-full lg:px-8 px-5 mb-8 lg:mt-12 mt-5 h-full flex-1">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Sidebar from '../../components/admin/Sidenav.vue'
export default{
    data(){
        return{
            isOpen: true
        }
    },
    components: {
        Sidebar
    },
    mounted(){
        this.resetIsOpen()
        window.addEventListener("resize", this.resetIsOpen);
    },
    onBeforeUnmount() {
        window.removeEventListener("resize", this.resetIsOpen);
    },
    methods: {
        toggleMenu(data) {
            this.isOpen = !this.isOpen;
        },
        resetIsOpen() {
            if (window.screen.width <= 1023) {
                this.isOpen = false;
            }else{
                this.isOpen = true;
            }
        },
    }
}
</script>
