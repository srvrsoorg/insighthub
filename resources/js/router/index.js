import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/store/auth'
import  { useSetupStore } from '@/store/setup'

import toast from '@/plugins/toast-notification'

import AdminRoutes from '@/router/admin'
import GuestRoutes from '@/router/guest'
import DashboardRoutes from '@/router/dashboard'
import ServerRoutes from '@/router/server'
import ApplicationRoutes from '@/router/application'

import GuestLayout from '@/pages/layouts/Guest.vue'

const routes = [
    ...AdminRoutes, // Include admin routes
    ...GuestRoutes, // Include guest routes
    ...DashboardRoutes, // Include user dashboard routes
    ...ServerRoutes, //Include server routes
    ...ApplicationRoutes, //Include application routes
    {
        path:'/:pathMatch(.*)*',
        name: '404',
        component: ()=> import('@/pages/PageNotFound.vue'),
        meta:{
            layout: GuestLayout,
            title:"Page Not Found"
        }
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

router.beforeEach(async(to, from, next) => {
    document.title = to.meta.title || '' // Set the document title based on the route meta


    const authStore = useAuthStore()
    const middleware = to.meta.middleware || []
    const hasSetupProperty = to.meta.hasOwnProperty('setupReqiures')

    if(hasSetupProperty){
        await useSetupStore().getSetupStatus()
        const setupComplete = useSetupStore().setupComplete

        if(to.meta.setupReqiures && !setupComplete){
            next({ name: 'checkPermissions' })
        }else if(!to.meta.setupReqiures && setupComplete){
            if(authStore.authenticated){
                next({name: 'dashboard'})
            }else{
                next({name: 'login'})
            }
        }else if(to.meta.setupReqiures && setupComplete){
            if(middleware && middleware.includes('guest')){
                // Handle middleware for guest
                if(authStore.authenticated){
                    next({name: 'dashboard'}) // Redirect to dashboard if already authenticated
                }else{
                    next()
                }
            }else if(middleware && middleware.includes('auth')){
                // Handle middleware for authenticated users
                if(!authStore.authenticated){
                    next({ name: 'login' }) // Redirect to login if not authenticated
                }else{
                    if(middleware.includes('admin') && !authStore.is_admin){
                        // Handle middleware for admin access
                        toast.error('You are not authorized to access this page!')
                        next({name: 'dashboard'}) // Redirect to dashboard if not an admin
                    }else{
                        next()
                    }
                }
            }else{
                next()
            }
        }else{
            next()
        }
    }else{
        next()
    }

})

export default router