<template>
    <div class="flex flex-col gap-6 bg-custom-50 rounded-md h-fit md:w-fit w-full py-5 md:px-6 px-4">
        <div class="flex items-center gap-4">
            <span :class="[$route.name == 'setupDatabase' ? 'text-white bg-custom-500' : 'bg-white' ,'flex items-center justify-center px-2.5 py-1.5 rounded-full']">
                <span class="material-symbols-outlined text-xl">
                    database
                </span>
            </span>
            <span :class="[$route.name == 'setupDatabase' ? 'text-custom-500' : '']">Database Credentials</span>
        </div>
        <div class="flex items-center gap-4">
            <span :class="[$route.name == 'keyVerification' ? 'text-white bg-custom-500' : 'bg-white' ,'flex items-center justify-center px-2.5 py-1.5 rounded-full']">
                <span class="material-symbols-outlined text-xl">
                    key
                </span>
            </span>
            <span :class="[$route.name == 'keyVerification' ? 'text-custom-500' : '']">License Key</span>
        </div>
        <div class="flex items-center gap-4">
            <span :class="[$route.name == 'setupSmtp' ? 'text-white bg-custom-500' : 'bg-white' ,'flex items-center justify-center px-2.5 py-1.5 rounded-full']">
                <span class="material-symbols-outlined text-xl">
                    mail
                </span>
            </span>
            <span :class="[$route.name == 'setupSmtp' ? 'text-custom-500' : '']">SMTP Credentials</span>
        </div>
        <div class="flex items-center gap-4">
            <span :class="[$route.name == 'register' ? 'text-white bg-custom-500' : 'bg-white' ,'flex items-center justify-center px-2.5 py-1.5 rounded-full']">
                <span class="material-symbols-outlined text-xl">
                    person
                </span>
            </span>
            <span :class="[$route.name == 'register' ? 'text-custom-500' : '']">Registration</span>
        </div>
        <div class="flex items-center gap-4">
            <span :class="[$route.name == 'setupSiteSettings' ? 'text-white bg-custom-500' : 'bg-white' ,'flex items-center justify-center px-2.5 py-1.5 rounded-full']">
                <span class="material-symbols-outlined text-xl">
                    settings
                </span>
            </span>
            <span :class="[$route.name == 'setupSiteSettings' ? 'text-custom-500' : '']">White Label Configuration</span>
        </div>
    </div>
</template>

<script>
export default {
    
}
</script>