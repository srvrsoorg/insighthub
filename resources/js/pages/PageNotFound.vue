<template>
    <div class="h-full flex justify-center">
        <div>
            <p class="text-xl font-cera-pro-bold leading-8 text-custom-600">404</p>
            <h1 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-5xl">Page not found</h1>
            <p class="mt-6 text-base leading-7 text-gray-600">Sorry, we couldn't find the page you're looking for.</p>
            <div class="mt-10">
                <a @click="$router.push({name:'dashboard'})" class="text-base font-cera-pro-bold leading-7 text-custom-600 cursor-pointer"><span aria-hidden="true">&larr;</span> Back to home</a>
            </div>
        </div>
    </div>
</template>
