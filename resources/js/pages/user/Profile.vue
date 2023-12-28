<template>
    <div class="container-fluid">
        <h2 class="text-2xl mt-7">
            Update Profile
        </h2>
        <form action="javascript:void(0)" class="xl:w-1/3 md:w-2/3 w-full bg-white p-5 mt-3 rounded-lg" @submit="updateProfile()">
            <div>
                <label for="name" class="block text-tiny text-neutral-600 font-medium">
                    Name
                </label>
                <div class="mt-1.5">
                    <input
                        id="name"
                        name="name"
                        type="text"
                        placeholder="Enter Name"
                        v-model="userData.name" 
                        class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                    />
                    <small id="name_message" class="error_message text-red-500"></small>
                </div>
            </div>
            <div class="mt-4">
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
                            v-model="userData.password" 
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
                            v-model="userData.password_confirmation" 
                            :class="{'tracking-widest': !showConfirmPassword}"
                            class="block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal text-sm leading-6 focus:ring-0"
                        />
                        <PasswordVisibility :showPassword="showConfirmPassword" @toggle="showConfirmPassword = !showConfirmPassword"/>
                    </div>
                    <small id="password_confirmation_message" class="error_message text-red-500"></small>
                </div>
            </div>
            <Button :disabled="processing" class="mt-5">
                <i v-if="processing" class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"></i>
                {{processing ? 'Please Wait' : 'Update'}}
            </Button>
        </form>
    </div>
</template>

<script>
import { mapActions, mapState } from 'pinia'
import {useAuthStore} from '@/store/auth'

export default {
    data(){
        return{
            userData: {
                name: '',
                password: '',
                password_confirmation: ''
            },
            processing: false,
            showPassword: false,
            showConfirmPassword: false
        }
    },
    computed:{
        ...mapState(useAuthStore, ['user'])
    },
    mounted(){
        this.userData.name = this.user.name
    },
    methods:{
        ...mapActions(useAuthStore, ['getUser']),
        async updateProfile(){
            this.hideError()
            this.processing = true
            await this.$axios.patch('/user/update', this.userData).then(({data}) => {
                this.$toast.success(data.message)
                this.getUser()
                this.userData.password = ''
                this.userData.password_confirmation = ''
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