import "../css/main.css";

import { createPinia } from "pinia";
import { useStyleStore } from "@/stores/style.js";
import { darkModeKey, styleKey } from "@/config.js";
import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy/dist/vue.m";


import { useToast } from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-sugar.css';


import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: import.meta.env.VITE_PUSHER_APP_KEY,
  cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
  //     wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
  //     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
  //     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
  //forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
  forceTLS: true,
  //     enabledTransports: ['ws', 'wss'],
});

const appName =
  window.document.getElementsByTagName("title")[0]?.innerText || "Laravel";

const pinia = createPinia();

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) =>
    resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob("./Pages/**/*.vue")
    ),
  setup({ el, App, props, plugin }) {
    return createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(pinia)
      .use(ZiggyVue, Ziggy)
      .mount(el);
  },
  progress: {
    color: "#00ff00",
  },
});

const styleStore = useStyleStore(pinia);

/* App style */
styleStore.setStyle(localStorage[styleKey] ?? "white");

/* Dark mode */
if (
  (!localStorage[darkModeKey] &&
    window.matchMedia("(prefers-color-scheme: dark)").matches) ||
  localStorage[darkModeKey] === "1"
) {
  styleStore.setDarkMode(true);
}
//window.Echo.private('meide') 
window.Echo.channel('meide') //Should be Channel Name
  .listen('MedideNotification', (e) => {
    //console.log(e);
    //let uid=document.getElementById("userinfo").getAttribute("user-id");
    if (e.module == '') {
      useToast().success(e.message, { duration: 20000 });
    }
  });
