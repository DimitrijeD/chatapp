<template>
    <div>
        <nav-bar></nav-bar>

        <div class="container mx-auto">
            <router-view></router-view>
        </div>

    </div>
</template>

<script>
import NavBar from './components/nav/NavBar.vue';
import Footer from './components/footer/Footer.vue';

export default {
    components: {
        'nav-bar': NavBar,
        'app-footer': Footer,
    },
}
</script>
