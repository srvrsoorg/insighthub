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
            Reset Password
        </h2>
        <form class="mt-4" action="javascript:void(0)" @submit="resetPassword()">
            <div>
                <label for="password" class="block text-tiny text-neutral-600 font-medium">
                    Password
                </label>
                <div class="mt-1.5">
                    <div class="relative">
                        <input
                            id="password"
                            name="password"
                            :type="showPassword ? 'text' : 'password'"
                            placeholder="Enter Password"
                            v-model="user.password" 
                            :class="{'tracking-widest': !showPassword}"
                            class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal text-sm leading-6 focus:ring-0"
                        />
                        <PasswordVisibility :showPassword="showPassword" @toggle="showPassword = !showPassword"/>
                    </div>
                    <small id="password_message" class="error_message text-red-500"></small>
                </div>
            </div>
            <div class="mt-4">
                <label for="password_confirmation" class="block text-tiny text-neutral-600 font-medium">
                    Confirm Password
                </label>
                <div class="mt-1.5">
                    <div class="relative">
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            :type="showConfirmPassword ? 'text' : 'password'"
                            placeholder="Enter Confirm Password"
                            v-model="user.password_confirmation" 
                            :class="{'tracking-widest': !showConfirmPassword}"
                            class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal text-sm leading-6 focus:ring-0"
                        />
                        <PasswordVisibility :showPassword="showConfirmPassword" @toggle="showConfirmPassword = !showConfirmPassword"/>
                    </div>
                    <small id="password_confirmation_message" class="error_message text-red-500"></small>
                </div>
            </div>
            <Button class="w-full mt-5" :disabled="processing">
                <i v-if="processing" class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"></i>
                {{processing ? 'Please Wait' : 'Reset Password'}}
            </Button>
        </form>
    </div>
</template>

<script>
export default{
    data(){
        return{
            user:{
                token: this.$route.params.token,
                password: '',
                password_confirmation: ''
            },
            processing: false,
            showPassword: false,
            showConfirmPassword: false
        }
    },
    methods: {
        async resetPassword(){
            this.hideError()
            this.processing = true

            await this.$axios.post('/reset-password', this.user).then(({data}) => {
                this.$toast.success(data.message)
                this.$router.push({
                    name: 'login'
                })
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