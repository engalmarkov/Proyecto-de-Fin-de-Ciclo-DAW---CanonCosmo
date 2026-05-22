/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./src/**/*.{html,ts}",
  ],
  theme: {
    extend: {
      colors: {
        'friki-dark': '#0f172a',
        'friki-card': '#1e293b',
        'friki-neon': '#22c55e', // Verde Neón
        'friki-accent': '#a855f7', // Morado
      },
      fontFamily: {
        'orbitron': ['Orbitron', 'sans-serif'],
        'inter': ['Inter', 'sans-serif'],
      }
    }
  },
  plugins: [],
}