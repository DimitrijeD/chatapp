<template>
    <div>
        <!-- Wrapper for opened and minimized chat windows-->
        <div class="small-container fixed bottom-0 inset-x-0">
            <div class="flex justify-start">
                <chat-window
                    v-for="group_id in openedGroupsIds"
                    :key="group_id"
                    :group_id="group_id"
                >
                </chat-window>
            </div>
        </div>
    </div>
</template>

<script>

import ChatWindow from "./ChatWindow/ChatWindow.vue";
import { mapGetters } from 'vuex';

export default {
    components:{
        'chat-window': ChatWindow,
    },

    data(){
        return{

        }
    },

    computed: {
        ...mapGetters({ 
            user: "StateUser",
            openedGroupsIds: "groups/openedGroupsIds",
            allGroups: "groups/allGroups"
        }),
    },

    created(){
        this.$store.dispatch('groups/getGroups')
        this.connectToActiveChatting();
    },


    methods:
    {
        connectToActiveChatting()
        {
            Echo.private("App.Models.User." + this.user.id)
            .listen('.message.notification', e => {
                const notif = e.messageNotification
                this.$store.dispatch('groups/openGroup', notif.group_id)
            });
        },

    },
}
</script>

<style>
/* Navigation dropdowns */
.slide-fade-nav-dropdowns-enter-active {
    transition: all .1s ease;
}
.slide-fade-nav-dropdowns-leave-active {
    transition: all .2s cubic-bezier(1.0, 0.5, 0.8, 1.0);
}
.slide-fade-nav-dropdowns-enter {
    transform: translateY(-5rem);
    opacity: 0;
}

.slide-fade-nav-dropdowns-leave-to {
    transform: translateY(-5rem);
    opacity: 0;
}
/* ------------------------------  */

/* Chat Window */
.slide-fade-chat-window-enter-active {
    transition: all .1s ease;
}
.slide-fade-chat-window-leave-active {
    transition: all .2s cubic-bezier(1.0, 0.5, 0.8, 1.0);
}
.slide-fade-chat-window-enter {
    transform: translateY(-5rem);
    opacity: 0;
}

.slide-fade-chat-window-leave-to {
    transform: translateY(5rem);
    opacity: 0;
}
/* ------------------------------  */

</style>