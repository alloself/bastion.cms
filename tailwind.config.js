const colors = require("tailwindcss/colors");

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./storage/framework/views/**/*.php",
        "./storage/framework/views/**/*.blade.php",
    ],
    theme: {
        extend: {
            colors: {
                light: "#fff",
                dark: "#000",
                brand: "#010620",
                primary: "#DD9D71",
                secondary: "#DBDDE2",
                neutral: "#B1B5C0",
                "neutral-100": "#EFEFEF",
                "neutral-alpha": "rgba(126, 126, 126, 0.40)",
                stone: "#B1B9CF",
                "stone-100": "#F3F3F3",
                "stone-200": "#E4E7EF",
                "stone-300": "#D9D9D9",
                accent: "#005954",
            },
            fontFamily: {
                base: "Inter, sans-serif",
            },
        },
    },
    plugins: [],
};
