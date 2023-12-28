<template>
    <CustomScrollbar class="h-full flex-1">
        <div class="flex-1 min-h-full bg-white py-8 ">
            <nav class="sm:px-6 px-4 space-y-1.5 h-full" aria-label="Sidebar">
                <template v-if="menuList.length > 0">
                    <template v-if="user && !showNarrowSidebar">
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
                    <BackToLink :to="{ name:'applications'}" text="Back to Applications"
                        v-if="$route.params.application"
                    />
                    <BackToLink :to="{ name:'servers'}" text="Back to Servers"
                        v-else-if="$route.params.server"
                    />
                    <template v-for="link in menuList" :key="link.id">
                        <template v-if="!link.children">
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
                        <Disclosure as="div" v-else class="space-y-1" v-slot="{open}" v-bind="{...openDefaultMenu(link)}">
                            <DisclosureButton :class="[open ? 'bg-custom-50 hover:bg-custom-50 text-custom-500 hover:text-custom-500' : ' text-gray-800 hover:bg-gray-100 hover:text-gray-900 ',' disabled:opacity-75 disabled:pointer-events-none group w-full flex items-center pl-2 pr-1 py-3 text-left text-tiny font-medium rounded-md focus:outline-none focus:ring-0']">
                                <span class="material-symbols-outlined text-[22px] mr-2">
                                    {{ link.icon }}
                                </span>
                                <span class="flex-1">{{ link.title }}</span>
                                <svg :class="[open ? 'text-gray-400 rotate-90' : 'text-gray-300', 'ml-3 h-5 w-5 flex-shrink-0 transform transition-colors duration-150 ease-in-out group-hover:text-gray-400 ']" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M6 6L14 10L6 14V6Z" fill="currentColor" />
                                </svg>
                            </DisclosureButton>
                            <DisclosurePanel>
                                <DisclosureButton v-for="subItem in link.children" :key="subItem.uri" as="a" class="group flex w-full items-center rounded-md pl-11 pr-2 text-tiny font-medium text-gray-600">
                                    <router-link :to="subItem.uri" exact-active-class="text-custom-500" class="w-full py-2 pl-2 rounded-md hover:text-custom-500" >{{ subItem.title }}</router-link>
                                </DisclosureButton>
                            </DisclosurePanel>
                        </Disclosure>
                    </template>
                    <template v-if="!showNarrowSidebar">
                        <hr class="!my-5"/>
                        <div>
                            <router-link :to="{name: 'profile'}" :class="['text-gray-800 hover:bg-gray-100 hover:text-gray-900 group w-full flex items-center px-2 py-3 text-tiny font-medium rounded-md']">
                                <span class="material-symbols-outlined text-[22px] mr-2">
                                    account_circle
                                </span>
                                Profile
                            </router-link>
                            <router-link v-if="is_admin" :to="{name: 'adminUsers'}" :class="['text-gray-800 hover:bg-gray-100 hover:text-gray-900 group w-full flex items-center px-2 py-3 text-tiny font-medium rounded-md']">
                                <span class="material-symbols-outlined text-[22px] mr-2">
                                    assignment_ind
                                </span>
                                Administrator
                            </router-link>
                            <div class="py-3 px-2">
                                <button v-if="!showNarrowSidebar" class="text-tiny flex items-center justify-center gap-1 text-gray-900" @click="logout">
                                    <span class="material-symbols-outlined text-[22px] mr-1">
                                        logout
                                    </span>
                                    Logout
                                </button>
                            </div>
                        </div>
                    </template>
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
import { useAuthStore } from '@/store/auth'
import { mapState, mapActions } from 'pinia'
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'

export default{
    props: ['menuList', 'showNarrowSidebar'],
    components: {
        Disclosure, DisclosureButton, DisclosurePanel
    },
    computed: {
        ...mapState(useAuthStore, ['user', 'is_admin'])
    },
    methods: {
        ...mapActions(useAuthStore, ['authLogout']),
        openDefaultMenu(row){
            let allLinks = [row.uri]
            
            if(row.children){
                const childLinks = row.children.map(row=>row.uri)
                allLinks = [...childLinks, ...allLinks]
            }
            if(allLinks.includes(this.$route.path)){
                return {
                    defaultOpen:true
                }
            }else{
                return {
                    defaultOpen:false
                }
            }
        },
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
