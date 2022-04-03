<template>
    <div>
        <nav-bar />
        <div v-if="user">
            <chat />
        </div>
        <div>
            <router-view />
        </div>

    </div>
</template>

<script>
import NavBar from './components/nav/NavBar.vue';
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