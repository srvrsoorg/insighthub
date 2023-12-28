import UserLayout from '@/pages/layouts/User.vue'

export default[
    {
        path:'/servers/:server/applications/:application',
        name:'applicationPanel',
        component: () => import('@/pages/user/application/Panel.vue'),
        meta: {
            middleware: ["auth"], // Middleware for authentication
            title: `Application`, // Title for the route
            layout: UserLayout,
            setupReqiures: true,
            showNarrowSidebar: true
        },
        children: [
            {
                path:'',
                name:'applicationDashboard',
                component: () => import('@/pages/user/application/Overview.vue'),
                meta: {
                    middleware: ["auth"], // Middleware for authentication
                    title: `Application Overview`, // Title for the route
                    layout: UserLayout,
                    setupReqiures: true,
                    showNarrowSidebar: true
                }
            }
        ]
    }
]