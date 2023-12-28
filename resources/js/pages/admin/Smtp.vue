<template>
    <div class="container-fluid">
        <h1 class="text-2xl">SMTP Settings</h1>
        <form action="javascript:void(0)" class="xl:w-1/2 md:w-2/3 w-full bg-white p-5 mt-3 rounded-lg" @submit="saveSettings()">
            <div class="grid sm:grid-cols-2 grid-cols-1 gap-x-5 gap-y-4">
                <div>
                    <label for="username" class="block text-tiny text-neutral-600 font-medium"
                        >Username</label
                    >
                    <input
                        type="text"
                        name="username"
                        v-model="smtp.username"
                        id="username"
                        class="mt-1.5 block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                        placeholder="Enter Username"
                    />
                    <small
                        class="text-red-500 error_message"
                        id="username_message"
                    ></small>
                </div>
                <div class="">
                    <label for="password" class="block text-tiny text-neutral-600 font-medium"
                        >Password</label
                    >
                    <input
                        type="password"
                        name="password"
                        v-model="smtp.password"
                        id="password"
                        class="mt-1.5 block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 placeholder:tracking-normal tracking-widest text-sm leading-6 focus:ring-0"
                        placeholder="Enter Password"
                    />
                    <small
                        class="text-red-500 error_message"
                        id="password_message"
                    ></small>
                </div>
                <div class="">
                    <label for="from_name" class="block text-tiny text-neutral-600 font-medium"
                        >From Name</label
                    >
                    <input
                        type="text"
                        name="from_name"
                        v-model="smtp.from_name"
                        id="from_name"
                        class="mt-1.5 block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                        placeholder="Enter From Name"
                    />
                    <small
                        class="text-red-500 error_message"
                        id="from_name_message"
                    ></small>
                </div>
                <div class="">
                    <label for="from_email" class="block text-tiny text-neutral-600 font-medium"
                        >From Email</label
                    >
                    <input
                        type="text"
                        name="from_email"
                        v-model="smtp.from_email"
                        id="from_email"
                        class="mt-1.5 block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                        placeholder="Enter From Email"
                    />
                    <small
                        class="text-red-500 error_message"
                        id="from_email_message"
                    ></small>
                </div>
                <div class="">
                    <label for="host" class="block text-tiny text-neutral-600 font-medium"
                        >Host</label
                    >
                    <input
                        type="text"
                        name="host"
                        v-model="smtp.host"
                        id="host"
                        class="mt-1.5 block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                        placeholder="Enter Host"
                    />
                    <small
                        class="text-red-500 error_message"
                        id="host_message"
                    ></small>
                </div>
                <div class="">
                    <label for="port" class="block text-tiny text-neutral-600 font-medium"
                        >Port</label
                    >
                    <input
                        type="text"
                        name="port"
                        v-model="smtp.port"
                        id="port"
                        class="mt-1.5 block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                        placeholder="Enter Port"
                    />
                    <small
                        class="text-red-500 error_message"
                        id="port_message"
                    ></small>
                </div>
                <div class="">
                    <label for="encryption" class="block text-tiny text-neutral-600 font-medium"
                        >Encryption</label
                    >
                    <input
                        type="text"
                        name="encryption"
                        v-model="smtp.encryption"
                        id="encryption"
                        class="mt-1.5 block w-full rounded-md border border-neutral-300 focus:border-neutral-300 py-1.5 text-gray-800 ring-gray-300 placeholder:text-gray-400 text-sm leading-6 focus:ring-0"
                        placeholder="Enter Encryption"
                    />
                    <small
                        class="text-red-500 error_message"
                        id="encryption_message"
                    ></small>
                </div>
            </div>
            <div class="mt-5 flex flex-wrap gap-5">
                <Button type="submit"
                    :disabled="processing"
                >
                    <i v-if="processing" class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"></i>
                    {{ processing ? "Please wait" : "Save Settings" }}
                </Button>
                <Button type="button"
                    :disabled="sending"
                    v-if="showTestMail"
                    @click="sendMail()"
                >
                    <i v-if="sending" class="fa-solid fa-circle-notch fa-spin mr-1 self-center inline-flex"></i>
                    {{ sending ? "Please wait" : "Send Test Mail" }}
                </Button>
            </div>
        </form>
    </div>
</template>

<script>
export default{
    data(){
        return{
            smtp: {
                host: '',
                port: '',
                username: '',
                password: '',
                from_name: '',
                from_email: '',
                encryption: ''
            },
            showTestMail: false,
            processing: false,
            sending: false
        }
    },
    created(){
        this.verifyApi(this.getSettings)
    },
    methods: {
        async getSettings(){
            this.$axios.get(`/admin/smtp`).then(({data}) => {
                if(data.smtp){
                    this.smtp = data.smtp
                    this.showTestMail= true
                }
            }).catch(({ response: { data } }) => {
                this.$toast.error(data.message);
            })
        },
        async saveSettings(){
            this.processing = true
            this.hideError();

            this.$axios.post(`/admin/smtp`, this.smtp).then(({data}) => {
                this.$toast.success(data.message);
                this.getSettings()
            }).catch(({ response }) => {
                if (response !== undefined) {
                    const { status, data } = response;
                    if (status === 422) {
                        this.displayError(data);
                    } else {
                        this.$toast.error(data.message);
                    }
                }
            })
            .finally(() => {
                this.processing = false;
            });
        },
        async sendMail(){
            this.sending = true
            this.$axios.get(`/admin/smtp/testMail`).then(({data}) => {
                this.$toast.success(data.message);
            }).catch(({ response: { data } }) => {
                this.$toast.error(data.message);
            }).finally(() => {
                this.sending = false
            })
        }
    }
}
</script>
