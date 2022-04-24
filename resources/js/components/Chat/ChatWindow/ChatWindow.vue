<template>
    <transition name="slide-fade-chat-window">
        <div class="window-w mr-3 flex flex-col"
            :class="{ 'flex-col-reverse': isMinimized }"
        >
            <!-- Chat Window Header -->
            <div class="flex flex-nowrap window-w h-16 bg-blue-500">
                <chat-participants
                    :participants="group.participants"
                />
                <window-management
                    :group_id="group.id"
                    :showConfig="showConfig"
                    @minimizeWindow="minimizeWindow()"
                    @openConfig="openConfig()"
                />
            </div>
            <!-- -------------------- -->


            <!-- wrapper for dynamic window minimization -->
            <div
                class="static border-l-2 border-r-2 border-blue-200 flex flex-col bg-white bg-gradient-to-r from-blue-50 via-white to-gray-100"
                :class="{ 'hidden': isMinimized }"
                @click="selfAcknowledged();"
            >
                <!-- <background /> -->
                <config 
                    :showConfig="showConfig"
                    :group="group"
                />

                <!-- Chat Window Body -->
                <div class="window-h">
                    <messages-block
                        :group="group"
                    />
                </div>
                <!-- -------------------- -->


                <!-- Chat Window Footer -->
                <message-input
                    :group_id="group.id"
                />
                <!-- -------------------- -->
            </div>
        </div>
    </transition>
</template>

<script>
import WindowManagement from "./Header/WindowManagement.vue";
import ChatParticipants from "./Body/ChatParticipants.vue";
import MessagesBlock    from "./Body/MessagesBlock";
import Config           from "./Body/Config.vue";
import MessageInput     from "./Footer/MessageInput.vue";

import { mapGetters } from 'vuex';

export default {
    props:[
        'group',
    ],

    data(){
        return {
            isMinimized: false,
            lastAcknowledgedMessageId: null,
            showConfig: false,
            // user: this.$store.state.auth.user,
        }
    },

    computed: {
        ...mapGetters({ 
            user: "StateUser",
        }),
    },

    components: {
        'chat-participants': ChatParticipants,
        'messages-block': MessagesBlock,
        'message-input': MessageInput,
        'window-management': WindowManagement,
        'config': Config,
    },

    created() {
        this.listenForNewMessages()
        this.listenForMessagesSeen()
    },

    methods: {   
        minimizeWindow(){
            this.isMinimized = !this.isMinimized
        },

        openConfig(){
            this.showConfig = !this.showConfig
        },

        getMessages(){
            this.$store.dispatch('groups/getMessages', {group_id: this.group.id })
        },

        findLatestMessageId: (messages) => Math.max(...Object.keys(messages)),

        isUserOwnerOfLastMessage: (message, userId) => message.user_id == userId ? true : false,

        listenForNewMessages()
        {
            this.getMessages();

            // this user is not listening to his own new message event
            Echo.private("group." + this.group.id)
            .listen('.message.new', e => {
                this.getMessages();
            });
        },

        // Event when user clicks his own window (he saw/red all messages)
        selfAcknowledged()
        {
            // Preventing user from acknowledging chat group with no messages. Whould be cool if
            // this was called only once (when there accualy are no messages)
            if(!Object.keys(this.group.messages).length) return 

            // Preventing user from acknowledging his own message.
            if(this.isUserOwnerOfLastMessage(this.group.messages[this.findLatestMessageId(this.group.messages)], this.user.id)) return false

            // or if user already acknowledged all messages.
            if(this.group.last_msg_id == this.lastAcknowledgedMessageId) return
            
            this.lastAcknowledgedMessageId = this.group.last_msg_id

            this.$store.dispatch('groups/setAllMessagesAcknowledged', {
                group_id: this.group.id,
                lastAcknowledgedMessageId: this.lastAcknowledgedMessageId
            })
        },

        listenForMessagesSeen()
        {
            Echo.private("group." + this.group.id)
            .listen('.message.seen', e => {
                this.$store.dispatch('groups/seenEvent', e.seenData)
            });
        },

    }
}

</script>

<style>
.window-h{
    height: 31rem;
}

.window-w{
    width: 29rem;
}


</style>
