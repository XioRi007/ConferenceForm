import VueSocialSharing from 'vue-social-sharing'
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import './assets/main.css'

const app = createApp(App);

app.use(router);
app.use(store);
app.use(VueSocialSharing);

app.mount('#app');
