import Home from './components/Home.vue';
import About from './components/About.vue';
import NotFound from "./components/NotFound.vue";
import Login from "./components/Login.vue";
import Register from "./components/Register.vue";
import Profile from "./components/Profile.vue";
import MailVerification from './components/MailVerification.vue';
import Chat from "./components/Chat/Chat.vue";

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
                axios.get('/api/authenticated')
                    .then((res) => {
                        // not logged in
                        if( !res.data ){
                            return next({ path: '/login' })
                        }
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
        },
    ]

}

