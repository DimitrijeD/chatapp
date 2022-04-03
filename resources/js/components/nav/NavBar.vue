<template>
    <div class="bg-blue-700">
        <div class="small-container flex justify-between py-3">

            <!-- Left side of navigation bar -->
            <div>
                <div v-if="user">
                    <router-link to="/"        class="mr-3 text-xl text-white" exact>Home</router-link>
                    <router-link to="/about"   class="mr-3 text-xl text-white">About</router-link>
                    <router-link to="/profile" class="     text-xl text-white">Profile</router-link>
                </div>

                <div v-else>
                    <router-link to="/"        class="mr-3 text-xl text-white" exact>Home</router-link>
                    <router-link to="/about"   class="     text-xl text-white">About</router-link>
                </div>
            </div>


            <!-- Right side of navigation bar -->
            <div>
                <div v-if="user">                    
                    <logout class="text-xl text-white"></logout>
                </div>

                <div v-else>
                    <router-link to="/login"    class="mr-3 text-xl text-white" exact>Login</router-link>
                    <router-link to="/register" class="     text-xl text-white">Register</router-link>
                </div>
            </div>


        </div>
    </div>
</template>

<script>
import Logout from './Logout.vue';
import { mapGetters } from 'vuex';

export default {
    components: {
        'logout': Logout,
    },

    computed: {
        ...mapGetters({ user: "StateUser" }),
    },
    
}
</script>
