import axios from "axios";

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
        userX: null,
    },

    mutations: {
        setUserX(state, userX)
        {
            state.userX = userX;
        },
    },

    actions: {
        async getUserX(context)
        {
            return await axios.get('/api/user').then((res)=>{
                context.commit('setUserX', res.data);
            });
        },
    },

});


const app = new Vue({
    el: '#app',
    router: new VueRouter(routes),
    store: store,
    render: h => h(App),

    beforeCreate() {
        this.$store.dispatch("getUserX");
    },
});


