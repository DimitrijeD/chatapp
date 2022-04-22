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
                    @messageSent="getMessages()"
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
        minimizeWindow()
        {
            this.isMinimized = !this.isMinimized
        },

        openConfig()
        {
            this.showConfig = !this.showConfig
        },

        /**
         * If there are messages in chat group, get only those which have greater ID than "lastMessageId" 
         *      then merge existing messages and received ones into 'this.messages' variable
         * else 
         *      there are no messages, get all messsages 
         * 
         */
        getMessages()
        {
            this.$store.dispatch('groups/getAllMessages', {group_id: this.group.id})
            //     console.log('this chat window already has messages, do "missing messages" get request');
            //     axios.get('/api/chat/group/' + this.group.id + '/from-msg/' + lastMessageId)
            //     .then(res => {

            //         /**
            //          * @TODO Before I finish this, I need to convert "this.messages" into hash table :
            //          * this.messages = {
            //          *      msgId1: { id: msgId1, text: 'older message text ' ...},
            //          *      msgId2: { id: msgId2, text: 'newer message text ' ...},
            //          *      ...
            //          * } 
            //          * this.messages = {
            //          *      23: { id: 23, text: 'older message text ' ...},
            //          *      75: { id: 75, text: 'newer message text ' ...},
            //          *      ...
            //          * }
            //          */
                     
            //     })
            //     .catch(error => {
            //         // 
            //     });
            // } else {
            //     // axios.get('/api/chat/group/' + this.group.id + '/messages')
            //     // .then(res => {
            //     //     this.messages = res.data;

            //     // })
            //     // .catch(error => {
            //     //     // 
            //     // });
        },

        listenForNewMessages()
        {
            this.getMessages();

            Echo.private("group." + this.group.id)
            .listen('.message.new', e => {
                // There is a new message in this chat group,
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

        findLatestMessageId(messages)
        {
            return Math.max(...Object.keys(messages))
        },

        isUserOwnerOfLastMessage(message, userId)
        {
            if(message.user_id == userId){
                return true;
            }
            return false;
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
