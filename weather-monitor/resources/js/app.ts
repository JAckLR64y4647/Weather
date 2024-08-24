import { createApp } from 'vue';
import App from '@/App.vue';
import { createPinia } from 'pinia';
import piniaPluginPersistedState from 'pinia-plugin-persistedstate';
import primeVue from 'primevue/config';
import primeVueTheme from '@/plugins/primevue';
import ToastService from 'primevue/toastservice';
import Tooltip from 'primevue/tooltip';
import '@/plugins/echarts';

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import '@/plugins/icons';

import router from '@/plugins/router';

import '@/assets/css/app.css';

import { createI18n } from 'vue-i18n';
import * as en from '@/assets/lang/en.json';
import * as uk from '@/assets/lang/uk.json';
import * as ja from '@/assets/lang/ja.json';

const app = createApp(App);

const pinia = createPinia();

pinia.use(piniaPluginPersistedState);

const i18n = createI18n({
	legacy: false, // you must set `false`, to use Composition API
	locale: 'uk',
	fallbackLocale: 'en',
	messages: {
		en,
		uk,
		ja,
	},
});

app.use(pinia)
  .use(primeVue, { unstyled: true, pt: primeVueTheme })
  .use(ToastService)
  .use(router)
	.use(i18n)
	.directive('tooltip', Tooltip)
  .component('font-awesome-icon', FontAwesomeIcon)
  .mount('#app');
