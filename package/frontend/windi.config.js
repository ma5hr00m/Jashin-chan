import { defineConfig } from 'windicss/helpers'
import scrollbar from '@windicss/plugin-scrollbar'

export default defineConfig({
    plugins: [
        scrollbar,
    ],
    theme: {
        extend: {
            colors: {
            mainBack: '#2f2f2f',
            blued: '#136EF1',
            bluel: '#1689FC',
            yellowd: '#FFCC33',
            yellowl: '#FEA832',
            }
        },
    },
})