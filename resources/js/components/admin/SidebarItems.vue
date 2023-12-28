<template>
    <CustomScrollbar class="h-full flex-1">
        <div class="flex-1 min-h-full bg-white py-8 ">
            <nav class="sm:px-6 px-4 space-y-1.5 h-full" aria-label="Sidebar">
                <template v-if="menuList.length > 0">
                    <template v-if="user">
                        <div class="flex gap-4 items-center mb-8 px-2">
                            <img
                                v-if="!logo"
                                class="h-10 w-auto"
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
                        <div class="flex gap-4 items-center !mb-6 px-2">
                            <img :src="user.avatar" class="w-11 h-11 rounded-lg"/>
                            <div class="flex flex-col">
                                <span class="text-tiny text-gray-800">{{ user.name }}</span>
                                <span class="text-[13px] text-zinc-500">{{ user.designation }}</span>
                            </div>
                        </div>
                    </template>
                    <template v-for="link in menuList" :key="link.id">
                        <router-link
                            :to="link.uri"
                            :exact-active-class="sidebarActiveLinks"
                            :class="['text-gray-800 hover:bg-gray-100 hover:text-gray-900 group w-full flex items-center px-2 py-3 text-tiny font-medium rounded-md']">
                            <span class="material-symbols-outlined text-[22px] mr-2">
                                {{ link.icon }}
                            </span>
                            {{ link.title }}
                        </router-link>
                    </template>
                    <hr class="!my-5"/>
                    <div class="">
                        <router-link :to="{name: 'dashboard'}" :class="['text-gray-800 hover:bg-gray-100 hover:text-gray-900 group w-full flex items-center px-2 py-3 text-tiny font-medium rounded-md']">
                            <span class="material-symbols-outlined text-[22px] mr-2">
                                assignment_ind
                            </span>
                            User
                        </router-link>
                        <div class="py-3 px-2">
                            <button class="flex items-center justify-center gap-1 text-gray-800 text-tiny" @click="logout">
                                <span class="material-symbols-outlined text-[22px] mr-1">
                                    logout
                                </span>
                                Logout
                            </button>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <li class=" list-none p-1" v-for="(index,key) in (new Array(15))" :key="key">
                        <Skeleton :count="1"/>
                    </li>
                </template>
            </nav>
        </div>
    </CustomScrollbar>
</template>

<script>
import { mapState, mapActions } from 'pinia'
import { useAuthStore } from '@/store/auth'

export default{
    props: ['menuList'],
    computed: {
        ...mapState(useAuthStore, ['user'])
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
