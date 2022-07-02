<template>
    <nav class="bg-blue-700">
        <div class="small-container flex justify-between ">

            <!-- Left side of navigation bar -->
            <div>
                <div v-if="user" class="flex justify-start">
                    <router-link to="/" style="text-decoration: none;" class="a-nab-b" >Home</router-link>
                    <router-link to="/profile" style="text-decoration: none;" class="a-nab-b">Profile</router-link>
                    <create-chat-group />
                    <group-list />
                </div>

                <div v-else class="flex justify-start">

                </div>
            </div>


            <!-- Right side of navigation bar -->
            <div>
                <div v-if="user" class="flex justify-start">                    
                    <logout style="text-decoration: none;" class="a-nab-b"></logout>
                </div>

                <div v-else class="flex justify-start">
                    <router-link to="/login" style="text-decoration: none;" class="a-nab-b">Login</router-link>
                    <router-link to="/register" style="text-decoration: none;" class="a-nab-b">Register</router-link>
                </div>
            </div>

        </div>
    </nav>
</template>

<script>
import Logout from './Logout.vue';
import { mapGetters } from 'vuex';
import GroupList from "../Chat/GroupList.vue";
import CreateChatGroup from "../Chat/CreateChatGroup.vue";

export default {
    components: {
        'logout': Logout,
        'group-list': GroupList,
        'create-chat-group': CreateChatGroup,
    },

    computed: {
        ...mapGetters({ user: "StateUser" }),
    },
    
}
</script>

<style scoped>
    .a-nab-b {
        @apply py-2 px-2 pr-2 text-xl text-blue-50 hover:text-white hover:bg-blue-800 font-medium;
    }

</style>