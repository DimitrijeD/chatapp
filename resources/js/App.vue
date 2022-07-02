<template>
    <div>
        <nav-bar />
        <chat 
            v-if="user"
        />
        <router-view />
    </div>
</template>

<script>
import NavBar from './components/MainNav/NavBar.vue';
import Footer from './components/footer/Footer.vue';
import Chat from "./components/Chat/Chat.vue";
import { mapGetters } from 'vuex';

export default {
    components: {
        'nav-bar': NavBar,
        'app-footer': Footer,
        'chat': Chat,
    },

    computed: {
        ...mapGetters({ user: "StateUser" }),
    },
}
</script>

<style>
    .small-container{
        margin-left: 20px;
        margin-right: 20px;
    }
</style>