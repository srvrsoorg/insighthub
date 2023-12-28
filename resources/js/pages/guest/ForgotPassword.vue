<template>
    <div class="w-full max-w-md mx-auto bg-white sm:px-8 px-6 py-6 shadow rounded-lg">
        <div class="sm:w-full sm:max-w-md">
            <img
                v-if="!logo"
                class="h-12 w-auto"
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
        <h2 class="text-2xl mt-7">
            Forgot Your Password
        </h2>
        <form class="space-y-4 mt-5" action="javascript:void(0)" @submit="forgotPassword()">
            <div>
                <label for="email" class="block text-tiny text-neutral-600 font-medium">
                    Email
                </label>
                <div class="mt-1.5">
                    <input
                        id="email"
                        name="email"
                        type="email"
                        placeholder="Enter Email"
                        v-model="email" 
                        class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                    />
                    <small id="email_message" class="error_message text-red-500"></small>
                </div>
            </div>
            <div>
                <Button class="w-full mt-2" :disabled="processing">
                    <i v-if="processing" class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"></i>
                    {{processing ? 'Please Wait' : 'Send Reset Password Link'}}
                </Button>
                <div class="text-center w-full mt-2">
                    <router-link class="text-zinc-500 hover:text-zinc-600 text-sm font-medium" :to="{
                        name: 'login'
                    }">Back to Login</router-link>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
export default{
    data(){
        return{
            email: '',
            processing: false
        }
    },
    methods: {
        async forgotPassword(){
            this.hideError()
            this.processing = true

            await this.$axios.post('/forgot-password', {
                email: this.email
            }).then(({data}) => {
                this.$toast.success(data.message)
                this.email = ''
            }).catch(({ response }) => {
                if (response !== undefined) {
                    const { status, data } = response;
                    if (status === 422) {
                        this.displayError(data);
                    } else {
                        this.$toast.error(data.message);
                    }
                }
            }).finally(() => {
                this.processing = false
            })
        }
    }
}
</script>