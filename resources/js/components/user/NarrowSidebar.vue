<template>
    <div class="no-scrollbar sticky top-0 bg-custom-500">
        <CustomScrollbar class="h-full">
            <nav class="block flex-shrink-0 w-20">
                <div class="relative flex w-20 flex-col space-y-3 px-4 py-8">
                    <div class="flex gap-4 items-center mb-8">
                        <img
                            v-if="!icon"
                            class="h-12 w-auto"
                            src="/logo/dark-logo-sm1.png"
                            :alt="app_name"
                        />
                        <img
                            v-else
                            class="w-auto"
                            :src="icon"
                            :alt="app_name"
                        />
                    </div>
                    <div class="!mb-6" v-if="user">
                        <img :src="user.avatar" class="w-11 h-11 rounded-lg"/>
                    </div>
                    <template v-for="link in menuList" :key="link.id">
                        <router-link 
                            :to="link.uri"
                            v-tooltip.right="`${link.title}`" 
                            :class="[textColorClass, sidebarHoverLinks, 'hover:bg-custom-100 group w-full flex items-center justify-center px-2 py-3 text-tiny font-medium rounded-md']">
                            <span class="material-symbols-outlined text-[22px]">
                                {{ link.icon }}
                            </span>
                        </router-link>
                    </template>
                    <hr :class="[isLightColor ? 'bg-gray-800' : 'bg-gray-100', '!my-5 border-0 h-px']"/>
                    <div class="space-y-1.5">
                        <router-link v-tooltip="'Profile'" :to="{name: 'profile'}" :class="[textColorClass, sidebarHoverLinks, 'hover:bg-custom-100 group w-full flex items-center justify-center px-2 py-3 text-tiny font-medium rounded-md']">
                            <span class="material-symbols-outlined text-[22px]">
                                account_circle
                            </span>
                        </router-link>
                        <router-link v-if="is_admin" v-tooltip="'Administrator'" :to="{name: 'adminUsers'}" :class="[textColorClass, sidebarHoverLinks, 'hover:bg-custom-100 group w-full flex items-center justify-center px-2 py-3 text-tiny font-medium rounded-md']">
                            <span class="material-symbols-outlined text-[22px]">
                                assignment_ind
                            </span>
                        </router-link>
                        <div class="w-full flex justify-center py-3">
                            <button v-tooltip="'Logout'" :class="[textColorClass, 'flex items-center justify-center gap-1']" @click="logout">
                                <span class="material-symbols-outlined text-[22px]">
                                    logout
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </nav>
        </CustomScrollbar>
    </div>
</template>

<script>
import { mapActions, mapState } from 'pinia'
import { useAuthStore } from '@/store/auth'
export default{
    props: ['menuList'],
    computed: {
        ...mapState(useAuthStore, ['user', 'is_admin'])
    },
    methods: {
        ...mapActions(useAuthStore, ['authLogout']),
        async logout(){
            await this.$axios.get('/user/logout').then(({data}) => {
                this.$toast.success(data.message)
                this.authLogout()
                this.$router.push('/login')
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            })
        }
    }
}
</script>
