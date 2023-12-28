export default {
    methods: {
        // Method to hide error messages and remove red borders from elements
        hideError() {
            $('input').removeClass('border-red-500')
            $('select').removeClass('border-red-500')
            $('textarea').removeClass('border-red-500')
            $('small.error_message').hide()
        },

        // Method to display error messages and add red borders to elements
        displayError(error) {
            $.each(error.errors, function (key, value) {
                key = key.replaceAll(".", "_")
                $(`#${key}`).addClass('border-red-500')
                $(`#${key}_message`).html(value[0]).fadeIn()
            })

        }
    }
}