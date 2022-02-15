import Home from './components/Home.vue';
import About from './components/About.vue';
import NotFound from "./components/NotFound.vue";
import Login from "./components/Login.vue";
import Register from "./components/Register.vue";
import Profile from "./components/Profile.vue";
import MailVerification from './components/MailVerification.vue';
import Chat from "./components/Chat/Chat.vue";

import store from './store/index.js';

export default {
    mode: 'history',
    linkActiveClass: 'font-semibold',
    routes: [
        {
            path: '*',
            component: NotFound,
            name: "NotFound",
        },

        {
            path: '/',
            component: Home,
        },

        {
            path: '/about',
            component: About,
            name: "About",
        },

        {
            path: '/login',
            component: Login,
            name: 'Login',
            beforeEnter: (to, from, next) => {
                axios.get('/api/user-loggedin')
                .then((res) => {
                    if( !res.data ){
                        // not logged in
                        next();
                    }
                });
            },
        },

        {
            path: '/register',
            component: Register,
            name: "Register",
            beforeEnter: (to, from, next) => {
                axios.get('/api/user-loggedin')
                .then((res) => {
                    if( res.data ){
                        // logged in
                    } else {
                        // not logged in
                        next();
                    }
                });
            },
        },

        {
            path: '/profile',
            component: Profile,
            beforeEnter: (to, from, next) => {
                // is user in store already?
                if(store.getters.StateUser){
                    return next();
                }

                // get user with verified email, false otherwise
                // user is not in store, try to get user, and if he is logged in, save him in store
                axios.get('/api/authenticated')
                .then((res) => {
                    let user = res.data; 
                    // if user is not logged in OR doesnt have email verified
                    if( !user ){
                        return next({ path: '/login' })
                    }

                    // commit user to store 
                    store.commit('setUser', user);
                    next();
                });
            },
        },

        {
            path: '/mail-verification/:slug?',
            component:  MailVerification,
        },

        {
            path: '/chat',
            component:  Chat,
            beforeEnter: (to, from, next) => {
                // is user in store already?
                if(store.getters.StateUser){
                    return next();
                }

                // get user with verified email, false otherwise
                // user is not in store, try to get user, and if he is logged in, save him in store
                axios.get('/api/authenticated')
                .then((res) => {
                    let user = res.data; 
                    // if user is not logged in OR doesnt have email verified
                    if( !user ){
                        return next({ path: '/login' })
                    }

                    // commit user to store 
                    store.commit('setUser', user);
                    next();
                });
            },
        },

    ]

}

