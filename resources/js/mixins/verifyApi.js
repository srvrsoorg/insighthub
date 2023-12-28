export default{
    data(){
        return{
            loading: false
        }
    },
    methods: {
        async verifyApi(callbackFn){
            this.loading = true
            await this.$axios.get(`/verify`).then(({data}) => {
                if(data.verify){
                    callbackFn()
                }
            }).catch(({response}) => {
                this.$toast.error(response.data.message)
            }).finally(() => {
                this.loading = false
            })
        },
    }
}