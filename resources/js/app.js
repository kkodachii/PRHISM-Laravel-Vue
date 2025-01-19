import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp, Head, Link} from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import VueTailwindDatepicker from "vue-tailwind-datepicker";
import OneSignalVuePlugin from '@onesignal/onesignal-vue3'

const appName = 'PRHISM';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>  resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(VueTailwindDatepicker)
            .use(OneSignalVuePlugin, {
                appId: '85e50d34-91f4-4726-af57-af564d2fd7a2',
              })
            .component('Head', Head)
            .component('Link', Link)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
        showSpinner: true,
    },
});

// if ('serviceWorker' in navigator) {
//     navigator.serviceWorker.register('/sw.js')
//       .then(function(registration) {
//         console.log('Service Worker registered with scope:', registration.scope);
//       }).catch(function(error) {
//         console.log('Service Worker registration failed:', error);
//     });
//   };
