import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createApp, h } from 'vue'
import { ZiggyVue } from 'ziggy-js'
import { createPinia } from 'pinia'
import '../css/app.css'
import Toast from './components/ui/toast/Toast.vue'

const appName = import.meta.env.VITE_APP_NAME || 'FamiliaMogi'
const pinia = createPinia()

createInertiaApp({
  title: (title) => (title ? `${title} - ${appName}` : appName),
  resolve: (name) =>
    resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })

    app.use(plugin)
    app.use(pinia)
    app.use(ZiggyVue)

    app.component('Toast', Toast)

    app.mount(el)
  },
  progress: {
    color: '#4B5563',
  },
})
