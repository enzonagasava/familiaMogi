import '../css/app.css'; // Importa o CSS global
import { createInertiaApp } from '@inertiajs/vue3'; // Core do Inertia.js
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'; // Auxilia no carregamento dinâmico de componentes
import type { DefineComponent } from 'vue'; // Tipagem do Vue
import { createApp, h } from 'vue'; // Funções do Vue
import { ZiggyVue } from 'ziggy-js'; // Integração com rotas Laravel
import { initializeTheme } from './composables/useAppearance'; // Lógica personalizada de tema (light/dark)

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

