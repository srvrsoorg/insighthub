export default{
    methods: {
        // Save Site Settings
        async saveSiteSettings(){
            this.processing = true
            this.hideError()

            var formData = new FormData()
            formData.append('app_name', this.payload.app_name)
            formData.append('color_code', this.payload.color_code.replace('#', ''));
            formData.append('retention_period', this.payload.retention_period)
            formData.append('redis_password', this.payload.redis_password)

            if (this.payload.logo != null && typeof this.payload.logo !== 'string') {
                formData.append('logo', this.payload.logo)
            } else {
                formData.append('logo', '')
            }

            if (this.payload.icon != null && typeof this.payload.icon !== 'string') {
                formData.append('icon', this.payload.icon)
            } else {
                formData.append('icon', '')
            }

            if (this.payload.favicon != null && typeof this.payload.favicon !== 'string') {
                formData.append('favicon', this.payload.favicon)
            } else {
                formData.append('favicon', '')
            }

            this.$axios.post('/admin/site-setting', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                }
            }).then(({data}) => {
                this.$toast.success(data.message)
                if(this.$route.meta.isAdminPage){
                    location.reload()
                }else{
                    this.$router.push('/')
                }
            }).catch(({ response }) => {
                if (response !== undefined) {
                    const { status, data } = response
                    if (status === 422) {
                        this.displayError(data)
                    } else {
                        this.$toast.error(data.message)
                    }
                }
            }).finally(() => {
                this.processing = false
            })
        }
    }
}