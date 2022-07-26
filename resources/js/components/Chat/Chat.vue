<template>
    <div class="small-container fixed bottom-0 inset-x-0"><!-- Wrapper for opened and minimized chat windows-->
        <div class="flex justify-start">
            <chat-window
                v-for="group_id in openedGroupsIds"
                :key="group_id"
                :group_id="group_id"
            >
            </chat-window>
        </div>
    </div>
</template>

<script>

import ChatWindow from "./ChatWindow/ChatWindow.vue";
import { mapGetters } from 'vuex';
import * as ns from '../../store/module_namespaces.js'

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
            user: "user",
            openedGroupsIds: ns.groupsManager() + "/openedGroupsIds",
        }),
    },

    beforeCreate(){

    },

    created(){
        this.$store.dispatch(ns.groupsManager() + '/init').then(()=>{
            this.$store.dispatch(ns.groupsManager() + '/sortNewstGroups')
            this.$store.dispatch(ns.groupsManager()+ '/numGroupsWithUnseen')
        })

        this.listenUserToUserNotifications()
    },

    methods:
    {
        listenUserToUserNotifications(){
            Echo.private("App.Models.User." + this.user.id)
            .listen('.message.notification', e => {
                this.$store.dispatch(ns.groupsManager() + '/openGroup', e.data.group_id)
            })
        },


    },
}
</script>

<style>

</style>