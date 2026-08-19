/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        primary: "#8B5CF6",
        "on-primary": "#FFFFFF",
        secondary: "#C4B5FD",
        accent: "#059669",
        background: "#FAF5FF",
        foreground: "#4C1D95",
        muted: "#EDEFF9",
        border: "#EDE9FE",
        destructive: "#DC2626",
        ring: "#8B5CF6",
      },
      fontFamily: {
        serif: ["Lora", "serif"],
        sans: ["Raleway", "sans-serif"],
      },
      spacing: {
        "space-1": "0.25rem",
        "space-2": "0.5rem",
        "space-3": "0.75rem",
        "space-4": "1rem",
        "space-6": "1.5rem",
        "space-8": "2rem",
        "space-12": "3rem",
        "space-16": "4rem",
        "space-24": "6rem",
        "space-32": "8rem",
      },
    },
  },
  plugins: [],
}
