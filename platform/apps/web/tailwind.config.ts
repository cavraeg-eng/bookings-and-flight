import type { Config } from "tailwindcss";
import forms from "@tailwindcss/forms";
import containerQueries from "@tailwindcss/container-queries";

const config: Config = {
    content: ["./src/**/*.{ts,tsx}"],
    theme: {
        container: {
            center: true,
            padding: {
                DEFAULT: "1.25rem",
                sm: "1.5rem",
                lg: "2rem",
            },
            screens: {
                "2xl": "1280px",
            },
        },
        extend: {
            colors: {
                // Deep ink (primary dark)
                ink: {
                    50: "#F2F5F9",
                    100: "#E1E8F1",
                    200: "#B9C7D8",
                    300: "#8BA0BB",
                    400: "#5E779B",
                    500: "#3A5375",
                    600: "#1F3556",
                    700: "#142647",
                    800: "#0E1D3A",
                    900: "#0B1628",
                    950: "#070F1E",
                },
                // Warm cream/bone neutrals (light backgrounds)
                cream: {
                    50: "#FEFCF7",
                    100: "#FBF6EC",
                    200: "#F5ECD7",
                    300: "#EBDEBD",
                    400: "#D9C79A",
                    500: "#C0AB78",
                },
                // Sunrise amber accent
                amber: {
                    50: "#FFF8EB",
                    100: "#FFE9C6",
                    200: "#FFD28A",
                    300: "#FFB74A",
                    400: "#FBA01F",
                    500: "#F08900",
                    600: "#D27000",
                    700: "#A95700",
                    800: "#7E3F00",
                    900: "#5E2E00",
                },
                // Sky mist (calm blue for accents)
                sky: {
                    50: "#F3F8FC",
                    100: "#E3EEF8",
                    200: "#C3DCEF",
                    300: "#94C3E2",
                    400: "#5DA3D0",
                    500: "#3685BA",
                    600: "#266B9A",
                    700: "#1F557B",
                    800: "#1C4362",
                },
            },
            fontFamily: {
                display: ["var(--font-display)", "Georgia", "serif"],
                sans: ["var(--font-sans)", "ui-sans-serif", "system-ui", "sans-serif"],
                mono: ["var(--font-mono)", "ui-monospace", "monospace"],
            },
            fontSize: {
                // Fluid type scale — mobile up to desktop via clamp()
                "display-xl": ["clamp(2.75rem, 5vw + 1rem, 5.5rem)", { lineHeight: "1.02", letterSpacing: "-0.03em" }],
                "display-lg": ["clamp(2.25rem, 4vw + 0.5rem, 4rem)", { lineHeight: "1.05", letterSpacing: "-0.025em" }],
                "display-md": ["clamp(1.75rem, 2.5vw + 0.5rem, 2.75rem)", { lineHeight: "1.1", letterSpacing: "-0.02em" }],
                "body-lg": ["clamp(1rem, 0.4vw + 0.9rem, 1.15rem)", { lineHeight: "1.6" }],
            },
            borderRadius: {
                xl: "14px",
                "2xl": "20px",
                "3xl": "28px",
                card: "1rem",
                search: "0.75rem",
                pill: "9999px",
            },
            boxShadow: {
                subtle: "0 1px 2px rgba(10, 22, 40, 0.06), 0 1px 3px rgba(10, 22, 40, 0.04)",
                card: "0 6px 20px -10px rgba(10, 22, 40, 0.25), 0 2px 6px rgba(10, 22, 40, 0.06)",
                cardHover: "0 12px 32px -8px rgba(10, 22, 40, 0.3), 0 4px 12px rgba(10, 22, 40, 0.08)",
                glass: "0 8px 32px rgba(10, 22, 40, 0.12), inset 0 1px 0 rgba(255, 255, 255, 0.1)",
                search: "0 4px 24px -4px rgba(10, 22, 40, 0.18), 0 2px 8px rgba(10, 22, 40, 0.06)",
                float: "0 30px 70px -35px rgba(10, 22, 40, 0.5), 0 10px 30px -15px rgba(10, 22, 40, 0.25)",
                inset: "inset 0 1px 0 rgba(255, 255, 255, 0.04)",
            },
            backdropBlur: {
                xs: "2px",
            },
            backgroundImage: {
                "grain": "url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='160' viewBox='0 0 160 160'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='2' stitchTiles='stitch'/%3E%3CfeColorMatrix values='0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.07 0'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E\")",
                "sky-wash": "radial-gradient(ellipse 100% 80% at 50% 0%, rgba(93, 163, 208, 0.15), transparent 60%)",
            },
            animation: {
                "fade-in": "fade-in 0.5s ease-out",
                "fade-up": "fade-up 0.55s cubic-bezier(0.22, 1, 0.36, 1)",
                "flip": "flip 0.35s ease-out",
                "shimmer": "shimmer 2.2s ease-in-out infinite",
                "fadeIn": "fadeIn 0.2s ease-out",
                "slideUp": "slideUp 0.3s ease-out",
                "slideDown": "slideDown 0.2s ease-out",
                "scaleIn": "scaleIn 0.2s ease-out",
            },
            keyframes: {
                "fade-in": {
                    "0%": { opacity: "0" },
                    "100%": { opacity: "1" },
                },
                "fade-up": {
                    "0%": { opacity: "0", transform: "translateY(14px)" },
                    "100%": { opacity: "1", transform: "translateY(0)" },
                },
                "flip": {
                    "0%": { transform: "rotateX(-90deg)", opacity: "0" },
                    "100%": { transform: "rotateX(0)", opacity: "1" },
                },
                "shimmer": {
                    "0%": { backgroundPosition: "-200% 0" },
                    "100%": { backgroundPosition: "200% 0" },
                },
                fadeIn: {
                    "0%": { opacity: "0" },
                    "100%": { opacity: "1" },
                },
                slideUp: {
                    "0%": { opacity: "0", transform: "translateY(8px)" },
                    "100%": { opacity: "1", transform: "translateY(0)" },
                },
                slideDown: {
                    "0%": { opacity: "0", transform: "translateY(-8px)" },
                    "100%": { opacity: "1", transform: "translateY(0)" },
                },
                scaleIn: {
                    "0%": { opacity: "0", transform: "scale(0.95)" },
                    "100%": { opacity: "1", transform: "scale(1)" },
                },
            },
        },
    },
    plugins: [forms, containerQueries],
};

export default config;
