/** @type {import('tailwindcss').Config} */
export default {
    content: [
		"./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
	],
    theme: {
        extend: {
            fontSize: {
                tiny: ["15px", "22px"],
            },
            fontFamily: {
                'cera-pro-bold': ['CeraProBold', 'sans-serif'],
                'cera-pro-light': ['CeraProLight', 'sans-serif'],
                'cera-pro-medium': ['CeraProMedium', 'sans-serif'],
            },
            colors: {
                custom: {
                    50: "var(--color50, #F1F1FE)",
                    100: "var(--color100, #DEDFFC)",
                    200: "var(--color200, #C2C3FA)",
                    300: "var(--color300, #A1A3F7)",
                    400: "var(--color400, #8183F4)",
                    500: "var(--color500, #020c7E)",
                    600: "var(--color600, #2326EB)",
                    700: "var(--color700, #1114BB)",
                    800: "var(--color800, #0B0D7E)",
                    900: "var(--color900, #05063D)",
                },
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
};
