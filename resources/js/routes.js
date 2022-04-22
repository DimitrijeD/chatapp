import Home from './components/Home.vue';
import About from './components/About.vue';
import NotFound from "./components/NotFound.vue";
import Login from "./components/Auth/Login.vue";
import Register from "./components/Auth/Register.vue";
import Profile from "./components/Profile.vue";
import EmailVerification from './components/Auth/EmailVerification.vue';
import EmailVerificationAttempt from './components/Auth/EmailVerificationAttempt.vue';

import store from './store/index.js';

export default {
    mode: 'history',
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
                if(store.getters.StateUser) return next()

                axios.get('/api/authenticated').then((res) => {
                    store.commit('setUser', res.data.user)
                    return next()
                }).catch(()=>{
                    return next({ path: '/login' })
                });
            },
        },

        {
            path: '/about',
            component: About,
            name: "About",
            beforeEnter: (to, from, next) => {
                if(store.getters.StateUser) return next()

                axios.get('/api/authenticated').then((res) => {
                    store.commit('setUser', res.data.user)
                    return next()
                }).catch(()=>{
                    return next({ path: '/login' })
                });
            },
        },

        {
            path: '/login',
            component: Login,
            name: 'Login',
            beforeEnter: (to, from, next) => {
                axios.get('/api/authenticated').then((res) => {
                    store.commit('setUser', res.data.user)
                    return next()
                })
                .catch((error) => {
                    if (error.response.status == 403 ) {
                        return next({ path: '/email-verification/init' })
                    }

                    if (error.response.status == 401 ) {
                        return next()
                    }
                });
            },
        },

        {
            path: '/register',
            component: Register,
            name: "Register",
            beforeEnter: (to, from, next) => {
                axios.get('/api/authenticated').then((res) => {
                    store.commit('setUser', res.data.user)
                    return next()
                })
                .catch((error) => {
                    if (error.response.status == 403 ) {
                        return next({ path: '/email-verification/init' })
                    }

                    if (error.response.status == 401 ) {
                        return next()
                    }
                });
            },
        },

        {
            path: '/profile',
            component: Profile,
            beforeEnter: (to, from, next) => {
                if(store.getters.StateUser) return next()

                axios.get('/api/authenticated').then((res) => {
                    store.commit('setUser', res.data.user)
                    return next()
                }).catch(()=>{
                    return next({ path: '/login' })
                });
            },
        },

        {
            name: "email-verification",
            path: '/email-verification/init',
            component: EmailVerification,
        },

        {
            path: '/email-verification/uid/:user_id/c/:code',
            component: EmailVerificationAttempt,
            beforeEnter: (to, from, next) => {
                // if user is not in store
                if(!store.getters.StateUser) return next()
                return next({ path: '/profile' })
            },
        },

    ]

}

