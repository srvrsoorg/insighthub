import UserLayout from '@/pages/layouts/User.vue'

export default[
    {
        path:'/servers/:server',
        name:'serverPanel',
        component: () => import('@/pages/user/server/Panel.vue'),
        meta: {
            middleware: ["auth"], // Middleware for authentication
            title: `Server`, // Title for the route
            layout: UserLayout,
            setupReqiures: true,
            showNarrowSidebar: true
        },
        children: [
            {
                path:'',
                name:'serverDashboard',
                component: () => import('@/pages/user/server/Overview.vue'),
                meta: {
                    middleware: ["auth"], // Middleware for authentication
                    title: `Overview`, // Title for the route
                    layout: UserLayout,
                    setupReqiures: true,
                    showNarrowSidebar: true
                }
            }
        ]
    },
    {
        path:'/servers/:server/services',
        name:'serverServices',
        component: () => import('@/pages/user/server/Services.vue'),
        meta: {
            middleware: ["auth"], // Middleware for authentication
            title: `Services`, // Title for the route
            layout: UserLayout,
            setupReqiures: true,
            showNarrowSidebar: true
        }
    }
]