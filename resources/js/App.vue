<template>
    <div>
        <nav-bar></nav-bar>

        <div>
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

<style>
    .small-container{
        margin-left: 20px;
        margin-right: 20px;
    }
</style>