<template>
    <div class="w-full max-w-lg mx-auto bg-white sm:px-8 px-6 py-6 shadow rounded-lg">
        <div class="sm:w-full sm:max-w-md">
            <img
                v-if="!logo"
                class="h-12 w-auto"
                src="/logo/logo2.png"
                :alt="app_name"
            />
            <img
                v-else
                class="h-12 w-auto"
                :src="logo"
                :alt="app_name"
            />
        </div>
        <h2 class="text-2xl mt-7">
            Permissions
        </h2>
        <div class="bg-zinc-100 rounded-md mt-4">
            <div class="text-sm px-5 py-3 flex gap-3 flex-wrap justify-between items-center">
                <span class="text-zinc-500">storage/app</span>
                <span class="rounded-r-md flex gap-2 items-center">
                    <i :class="{'text-gray-500 fa-solid fa-circle-notch fa-spin': processing, 'text-green-500 fa-square-check': permission.app, 'text-red-500 fa-square-xmark': !permission.app}" class="fas"></i>
                    775
                </span>
            </div>
        </div>
        <div class="bg-zinc-100 rounded-md mt-2">   
            <div class="text-sm px-5 py-3 flex gap-3 flex-wrap justify-between items-center">
                <span class="text-zinc-500">storage/framework</span>
                <span class="flex gap-2 items-center">
                    <i :class="processing ? 'text-gray-500 fa-solid fa-circle-notch fa-spin' : permission.framework ? 'text-green-500 fa-square-check' : 'text-red-500 fa-square-xmark'" class="fas"></i>
                    775
                </span>
            </div>
        </div>
        <div class="bg-zinc-100 rounded-md mt-2">   
            <div class="text-sm px-5 py-3 flex gap-3 flex-wrap justify-between items-center">
                <span class="text-zinc-500">storage/logs</span>
                <span class="flex gap-2 items-center">
                    <i :class="processing ? 'text-gray-500 fa-solid fa-circle-notch fa-spin' : permission.log ? 'text-green-500 fa-square-check' : 'text-red-500 fa-square-xmark'" class="fas"></i>
                    775
                </span>
            </div>
        </div>
        <div class="bg-zinc-100 rounded-md mt-2">   
            <div class="text-sm px-5 py-3 flex gap-3 flex-wrap justify-between items-center">
                <span class="text-zinc-500">storage/framework/cache</span>
                <span class="rounded-br-md flex gap-2 items-center">
                    <i :class="processing ? 'text-gray-500 fa-solid fa-circle-notch fa-spin' : permission.cache ? 'text-green-500 fa-square-check' : 'text-red-500 fa-square-xmark'" class="fas"></i>
                    775
                </span>
            </div>
        </div>
        <div class="mt-5 text-end">
            <Button @click="goToNextStep()" class="mt-2" :disabled="disableButton">
                Next
            </Button>
        </div>
    </div>
</template>

<script>
export default{
    data(){
        return{
            processing: false,
            disableButton: true,
            permission:{
                app: false,
                cache: false,
                framework: false,
                log: false
            }
        }
    },
    created(){
        this.checkPermission()
    },
    methods: {
        async checkPermission(){
            this.processing = true
            await this.$axios.get("/setup/permission").then(({ data }) => {
                this.permission = data.permission

                // Check if all properties in permission object are true
                const allPropertiesTrue = Object.values(this.permission).every(value => value === true);
                this.disableButton = !allPropertiesTrue;

            }).catch(({ response }) => {
                this.$toast.error(response.data.message);
            }).finally(() => {
                this.processing = false
            })
        },
        goToNextStep(){
            this.$router.push({
                name: 'setupDatabase'
            })
        }
    }
}
</script>
