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
        <h2 class="text-3xl mt-7">
            Login
        </h2>
        <p class="text-gray-500 text-sm mt-1">Log in to continue access pages</p>
        <form class="mt-5" action="javascript:void(0)" @submit="login()">
            <div>
                <label
                    for="email"
                    class="block text-tiny text-neutral-600 font-medium"
                    >Email</label
                >
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
            <div class="mt-4">
                <label
                    for="password"
                    class="block text-tiny text-neutral-600 font-medium"
                    >Password</label
                >
                <div class="mt-1.5">
                    <div class="relative ">
                        <input
                            id="password"
                            name="password"
                            :type="showPassword ? 'text' : 'password'"
                            placeholder="Enter Password"
                            v-model="password"
                            :class="{'tracking-widest': !showPassword}"
                            class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal text-sm leading-6 focus:ring-0"
                        />
                        <PasswordVisibility :showPassword="showPassword" @toggle="showPassword = !showPassword"/>
                    </div>
                    <small id="password_message" class="error_message text-red-500"></small>
                </div>
                <div class="flex items-center justify-end mt-1.5" v-if="smtpComplete">
                    <div class="text-sm leading-6">
                        <router-link
                            :to="{ name: 'forgotPassword' }"
                            class="font-medium text-custom-500 hover:text-custom-600"
                        >
                            Forgot Password?
                        </router-link>
                    </div>
                </div>
            </div>
            <div class="text-end mt-5">
                <Button class="text-tiny w-full" :disabled="processing">
                    <i v-if="processing" class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"></i>
                    {{processing ? 'Please Wait' : ' Login'}}
                </Button>
            </div>
        </form>
    </div>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useAuthStore } from "@/store/auth";
import {useSetupStore} from "@/store/setup";
export default {
    data() {
        return {
            email: "",
            password: "",
            processing: false,
            showPassword: false
        };
    },
    computed: {
        ...mapState(useSetupStore, ['smtpComplete'])
    },
    methods: {
        ...mapActions(useAuthStore, [
            "setAuthenticated",
            "setAccessToken",
            "setUser",
            "setIsAdmin"
        ]),
        async login() {
            this.hideError();
            this.processing = true

            await this.$axios.post("/login", {
                email: this.email,
                password: this.password,
            }).then(({ data }) => {
                this.$toast.success(data.message);
                this.setAccessToken(data.token);
                this.setUser(data.user);
                this.setIsAdmin(data.is_admin)
                this.setAuthenticated(true);
                this.$router.push("/");
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
            });
        },
    },
};
</script>
