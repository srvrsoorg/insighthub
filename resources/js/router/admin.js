import AdminLayout from '@/pages/layouts/Admin.vue'

export default[
    {
        path:'/setup/site-settings',
        name:'setupSiteSettings',
        component: () => import('@/pages/SiteSettings.vue'),
        meta: {
            middleware: ["auth", "admin"], // Middleware for authentication and admin role
            title: `Site Settings`, // Title for the route
            setupReqiures: false
        }
    },
    {
        path:'/admin',
        name:'adminUsers',
        component: () => import('@/pages/admin/Users.vue'),
        meta: {
            middleware: ["auth", "admin"], // Middleware for authentication and admin role
            title: `Users`, // Title for the route
            setupReqiures: true,
            layout: AdminLayout
        }
    },
    {
        path:'/admin/users/:user/permissions',
        name:'adminUserPermissions',
        component: () => import('@/pages/admin/UserPermissions.vue'),
        meta: {
            middleware: ["auth", "admin"], // Middleware for authentication and admin role
            title: `User Permissions`, // Title for the route
            setupReqiures: true,
            layout: AdminLayout
        }
    },
    {
        path:'/admin/smtp-settings',
        name:'adminSmtpDetail',
        component: () => import('@/pages/admin/Smtp.vue'),
        meta: {
            middleware: ["auth", "admin"], // Middleware for authentication and admin role
            title: `SMTP Details`, // Title for the route
            setupReqiures: true,
            layout: AdminLayout
        }
    },
    {
        path:'/admin/settings',
        name:'adminSettings',
        component: () => import('@/pages/admin/Settings.vue'),
        meta: {
            middleware: ["auth", "admin"], // Middleware for authentication and admin role
            title: `Settings`, // Title for the route
            setupReqiures: true,
            layout: AdminLayout,
            isAdminPage: true
        }
    },
    {
        path:'/admin/servers',
        name:'adminServers',
        component: () => import('@/pages/admin/Servers.vue'),
        meta: {
            middleware: ["auth", "admin"], // Middleware for authentication and admin role
            title: `Servers`, // Title for the route
            setupReqiures: true,
            layout: AdminLayout
        }
    },
    {
        path:'/admin/applications',
        name:'adminApplications',
        component: () => import('@/pages/admin/Applications.vue'),
        meta: {
            middleware: ["auth", "admin"], // Middleware for authentication and admin role
            title: `Applications`, // Title for the route
            setupReqiures: true,
            layout: AdminLayout
        }
    }
]