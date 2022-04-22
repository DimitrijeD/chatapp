require('./bootstrap');

import "tailwindcss/tailwind.css";
import Vue from 'vue';
import VueRouter from 'vue-router';

import routes from './routes';
import App from "./App.vue";
import store from './store/index.js';

Vue.use(VueRouter);

const app = new Vue({
    el: '#app',
    router: new VueRouter(routes),
    store: store,
    render: h => h(App),
});


