import UserLayout from '@/pages/layouts/User.vue'

export default[
    {
        path:'/',
        name:'dashboard',
        component: () => import('../pages/user/MainDashboard.vue'),
        meta: {
            middleware: ["auth"], // Middleware for authentication
            title: `Dashboard`, // Title for the route
            layout: UserLayout,
            setupReqiures: true
        }
    },
    {
        path:'/servers',
        name:'servers',
        component: () => import('@/pages/user/Servers.vue'),
        meta: {
            middleware: ["auth"], // Middleware for authentication
            title: `Servers`, // Title for the route
            layout: UserLayout,
            setupReqiures: true
        }
    },
    {
        path:'/applications',
        name:'applications',
        component: () => import('@/pages/user/Applications.vue'),
        meta: {
            middleware: ["auth"], // Middleware for authentication
            title: `Applications`, // Title for the route
            layout: UserLayout,
            setupReqiures: true
        }
    },
    {
        path:'/profile',
        name:'profile',
        component: () => import('@/pages/user/Profile.vue'),
        meta: {
            middleware: ["auth"], // Middleware for authentication
            title: `Profile`, // Title for the route
            layout: UserLayout,
            setupReqiures: true
        }
    }
]