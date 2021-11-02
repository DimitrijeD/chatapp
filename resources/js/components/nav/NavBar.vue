<template>
    <div class="flex bg-gray-100 border border-gray-250 py-4">
        <div class="container mx-auto flex justify-between">

            <div class="">
                <router-link to="/" class="mr-2 text-xl" exact>Home</router-link>
                <router-link to="/about" class="mr-2 text-xl">About</router-link>
                <router-link to="/profile" class="text-xl">Profile</router-link>
            </div>

            <div class="flex">
                <!-- here i need, if user logedin show logout, if not show login register, but how do i get user data here -->
                <router-link to="/login" class="mr-3 text-xl" exact>Login</router-link>
                <router-link to="/register" class="mr-3 text-xl">Register</router-link>

                <logout class="text-xl"></logout>
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
