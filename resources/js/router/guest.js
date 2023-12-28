import GuestLayout from '@/pages/layouts/Guest.vue'
export default[
    {
        path:'/login',
        name:'login',
        component: () => import('@/pages/guest/Login.vue'),
        meta: {
            middleware: ["guest"], // Middleware for guest access 
            title: `Login`, // Title for the route
            layout: GuestLayout,
            setupReqiures: true
        },
    },
    {
        path:'/register',
        name:'register',
        component: () => import('@/pages/guest/Register.vue'),
        meta: {
            middleware: ["guest"], // Middleware for guest access
            title: `Register`, // Title for the route
            layout: GuestLayout,
            setupReqiures: false
        }
    },
    {
        path:'/forgot-password',
        name:'forgotPassword',
        component: () => import('@/pages/guest/ForgotPassword.vue'),
        meta: {
            middleware: ["guest"], // Middleware for guest access
            title: `Forget Password`, // Title for the route
            layout: GuestLayout,
            setupReqiures: true
        }
    },
    {
        path:'/reset-password/:token',
        name:'resetPassword',
        component: () => import('@/pages/guest/ResetPassword.vue'),
        meta: {
            middleware: ["guest"], // Middleware for guest access
            title: `Reset Password`, // Title for the route
            layout: GuestLayout,
            setupReqiures: true
        }
    },
    {
        path:'/setup/start',
        name:'setupStart',
        component: () => import('@/pages/guest/setup/Start.vue'),
        meta: {
            middleware: ["guest"], // Middleware for guest access
            title: `Setup`, // Title for the route
            layout: GuestLayout,
            setupReqiures: false
        },
    },
    {
        path:'/setup/database',
        name:'setupDatabase',
        component: () => import('@/pages/guest/setup/Database.vue'),
        meta: {
            middleware: ["guest"], // Middleware for guest access
            title: `Database Setup`, // Title for the route
            layout: GuestLayout,
            setupReqiures: false
        }
    },
    {
        path:'/setup/key-verification',
        name:'keyVerification',
        component: () => import('@/pages/guest/setup/KeyVerification.vue'),
        meta: {
            middleware: ["guest"], // Middleware for guest access
            title: `Key Verification`, // Title for the route
            layout: GuestLayout,
            setupReqiures: false
        }
    },
    {
        path:'/setup/permissions',
        name:'checkPermissions',
        component: () => import('@/pages/guest/setup/Permissions.vue'),
        meta: {
            middleware: ["guest"], // Middleware for guest access
            title: `Permissions`, // Title for the route
            layout: GuestLayout,
            setupReqiures: false
        }
    },
    {
        path:'/setup/smtp',
        name:'setupSmtp',
        component: () => import('@/pages/guest/setup/SMTP.vue'),
        meta: {
            middleware: ["guest"], // Middleware for guest access
            title: `Setup SMTP Credentials`, // Title for the route
            layout: GuestLayout,
            setupReqiures: false
        }
    }
]