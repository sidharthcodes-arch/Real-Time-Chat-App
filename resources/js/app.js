import './bootstrap';
// import './echo'; // <-- uncomment once Echo is configured (see echo.js)

import { createApp } from 'vue';
import ChatApp from './components/ChatApp.vue';

createApp(ChatApp).mount('#app');
