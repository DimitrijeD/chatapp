require('./bootstrap');

import "tailwindcss/tailwind.css"

import Vue from 'vue';
import VueRouter from 'vue-router';
import routes from './routes';
import App from "./App.vue";
import Vuex from 'vuex'

Vue.use(Vuex);
Vue.use(VueRouter);

const store = new Vuex.Store({
    state: {
        count: 0,
        user: null,
    },
    mutations: {
        increment (state) {
            state.count++
        },
    }
});

const app = new Vue({
    el: '#app',
    router: new VueRouter(routes),
    store: store,
    render: h => h(App),
});


