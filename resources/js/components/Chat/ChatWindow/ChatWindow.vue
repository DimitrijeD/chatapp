<template>
    <transition name="slide-fade-chat-window">
        <div class="window-x flex-none mr-3 box-border"
            :class="{ 'flex flex-col-reverse': isMinimized }"
        >
            <!-- Chat Window Header -->
            <div class="flex flex-nowrap gap-2 h-16 bg-blue-500">
                <chat-participants
                    :group="group"
                />
                <window-management
                    :group_id="group.id"
                    :showConfig="showConfig"
                    :isMinimized="isMinimized"
                    @minimizeWindow="minimizeWindow()"
                    @openConfig="openConfig()"
                />
            </div>
            <!-- -------------------- -->


            <!-- wrapper for dynamic window minimization -->
            <div
                class="static flex not-header-y flex-col border-l-2 border-r-2 border-b-2 border-blue-400"
                :class="{ 'hidden': isMinimized }"
                @click="selfAcknowledged();"
            >
                <config 
                    :showConfig="showConfig"
                    :group="group"
                    class="window-x h-full"
                />

                <!-- Chat Window Body -->
                <div class="body-y">
                    <messages-block
                        :group="group"
                    />
                </div>
                <!-- -------------------- -->


                <!-- Chat Window Footer -->
                <div class="border-t-4 border-gray-200 bg-gray-100">
                    <message-input
                        :group_id="group.id"
                    />
                </div>
                <!-- -------------------- -->
            </div>
        </div>
    </transition>
</template>

<script>
import WindowManagement from "./Header/WindowManagement.vue";
import ChatParticipants from "./Header/ChatParticipants.vue";
import MessagesBlock    from "./Body/MessagesBlock";
import Config           from "./Body/Config.vue";
import MessageInput     from "./Footer/MessageInput.vue";

import { mapGetters } from 'vuex';

export default {
    props:[
        'group_id',
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

        group(){ 
            return this.$store.getters['groups/filterById'](this.group_id)
        },

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
                this.getMessages()
            })
        },

        // Event when user clicks his own window (he saw/red all messages)
        selfAcknowledged()
        {
            // Preventing user from acknowledging chat group with no messages. Whould be cool if
            // this was called only once (when there accualy are no messages)
            if(!Object.keys(this.group.messages).length) return 

            if(this.group?.hasUnseenState)
                this.messageSeen()

            // Preventing user from acknowledging his own message.
            if(this.isUserOwnerOfLastMessage(this.group.messages[this.findLatestMessageId(this.group.messages)], this.user.id)) return

            // or if user already acknowledged all messages.
            if(this.group.last_msg_id == this.lastAcknowledgedMessageId) return
            
            this.messageSeen()
        },

        messageSeen()
        {
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
.window-x{
    width: 464px;
}

.window-y{
    height: 588.5px;
}

.not-header-y{
    height: 588.5px;
}

.body-y {
    height: 496px;
}

</style>
