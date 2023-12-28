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
            Insighthub
        </h2>
        <div class="mt-4">
            <label for="domain" class="block text-tiny">
                Domain
            </label>
            <div class="w-full px-3 py-3 text-sm mt-2 truncate bg-zinc-100 rounded-md text-stone-500">
                {{ domain }}
            </div>
        </div>
        <div class="mt-5">
            <label for="instruction" class="block text-tiny">
                Instructions
            </label>
            <ul class="list-disc w-full space-y-1 pl-7 pr-3 py-3 text-sm mt-2 bg-zinc-100 rounded-md text-gray-900"> 
                <li>
                    <span class="text-stone-500">Lorem ipsum dolor sit amet consectetur adipisicing elit.</span>
                </li>
                <li>
                    <span class="text-stone-500">Lorem ipsum dolor sit amet consectetur adipisicing elit.</span>
                </li> 
                <li>
                    <span class="text-stone-500">Lorem ipsum dolor sit amet consectetur adipisicing elit.</span>
                </li>
            </ul>
        </div>
        <div class="flex justify-end gap-3 mt-6">
            <router-link class="text-custom-500 border border-custom-500 rounded-md px-3 py-1.5 text-sm font-medium flex items-center" :to="{
                name: 'checkPermissions'
            }">
                <span class="material-symbols-outlined -ml-1.5"> chevron_left</span>
                Back
            </router-link>
            <Button class="" @click="startSetup()">Next</Button>
        </div>
    </div>
</template>

<script>
export default{
    data(){
        return{
            domain: window.location.host
        }
    },
    methods: {
        startSetup(){
            this.$router.push({
                name: 'setupDatabase'
            })
        }
    }
}
</script>
