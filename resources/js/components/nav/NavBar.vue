<template>
    <div class="flex bg-blue-700 py-3">
        <div class="container mx-auto flex justify-between">

            <div class="">
                <router-link to="/" class="mr-2 text-xl text-white" exact>Home</router-link>
                <router-link to="/about" class="mr-2 text-xl text-white">About</router-link>
                <router-link to="/profile" class="mr-2 text-xl text-white">Profile</router-link>
                <router-link to="/chat" class="text-xl text-white">Chat</router-link>
            </div>

            <div class="flex">
                <!-- here i need, if user logedin show logout, if not show login register, but how do i get user data here -->
                <router-link to="/login" class="mr-3 text-xl text-white" exact>Login</router-link>
                <router-link to="/register" class="mr-3 text-xl text-white">Register</router-link>

                <logout class="text-xl text-white"></logout>
            </div>
        </div>
    </div>
</template>

<script>
import Logout from './Logout.vue';
export default {
    components: {
        'logout': Logout,
    }
}
</script>
