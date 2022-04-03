import Home from './components/Home.vue';
import About from './components/About.vue';
import NotFound from "./components/NotFound.vue";
import Login from "./components/Login.vue";
import Register from "./components/Register.vue";
import Profile from "./components/Profile.vue";
import MailVerification from './components/MailVerification.vue';

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
            beforeEnter: (to, from, next) => {
                if(store.getters.StateUser){
                    return next();
                }

                axios.get('/api/authenticated')
                .then((res) => {
                    let user = res.data; 
                    if( !user ){
                        return next({ path: '/login' })
                    }
                    store.commit('setUser', user);
                    return next();
                });
            },
        },

        {
            path: '/about',
            component: About,
            name: "About",
            beforeEnter: (to, from, next) => {
                if(store.getters.StateUser){
                    return next();
                }

                axios.get('/api/authenticated')
                .then((res) => {
                    let user = res.data; 
                    if( !user ){
                        return next({ path: '/login' })
                    }
                    store.commit('setUser', user);
                    return next();
                });
            },
        },

        {
            path: '/login',
            component: Login,
            name: 'Login',
            beforeEnter: (to, from, next) => {
                axios.get('/api/user-loggedin')
                .then((res) => {
                    if(res.data){
                        if(!store.getters.StateUser){
                            axios.get('/api/authenticated')
                            .then((res) => {
                                let user = res.data; 
                                if( !user ){
                                    return next({ path: '/login' })
                                }

                                // commit user to store 
                                store.commit('setUser', user);
                                next({ path: '/profile' })
                            });
                        }
                    }
                    next();
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
                    if( !res.data ){
                        next();
                    }
                });
            },
        },

        {
            path: '/profile',
            component: Profile,
            beforeEnter: (to, from, next) => {
                if(store.getters.StateUser){
                    return next();
                }

                axios.get('/api/authenticated')
                .then((res) => {
                    let user = res.data; 
                    if( !user ){
                        return next({ path: '/login' })
                    }
                    store.commit('setUser', user);
                    return next();
                });
            },
        },

        {
            path: '/mail-verification/:slug?',
            component:  MailVerification,
        },

    ]

}

