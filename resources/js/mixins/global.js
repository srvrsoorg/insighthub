import toast from '@/plugins/toast-notification'

export default{
    data(){
        const siteSettings = window.siteSettings || {};
        const { logo, icon, favicon, color_code, app_name = 'InsightHub' } = siteSettings;

        return{
            siteSettings,
            logo: logo || null,
            icon: icon || null,
            favicon: favicon || null,
            color_code: color_code || '020C7E',
            app_name
        }
    },
    methods: {
        // Method to copy data to the clipboard
        copyToClipboard(dataToCopy) {
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(dataToCopy).select();
            document.execCommand("copy");
            $temp.remove();
            toast.info('Copied to clipboard!',{
                timeout: 2000,
                pauseOnFocusLoss: false,
                pauseOnHover: false,
            })
        }
    }
}