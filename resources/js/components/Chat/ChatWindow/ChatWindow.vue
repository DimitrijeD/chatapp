<template>
    <!-- Entire chat window wrapper -->
    <div class="window-w mr-1.5 flex flex-col"
        :class="{ 'flex-col-reverse': isMinimized }"
    >
        <!-- Chat Window Header -->
        <div class="flex flex-nowrap window-w h-16 bg-blue-500">
            <chat-participants
                :participants="chatGroup.participants"
            />
            <window-management
                :windowIndex="windowIndex"
                v-on="$listeners"
                @minimizeWindow = "minimizeWindow()"
            />
        </div>
        <!-- -------------------- -->


        <!-- wrapper for dynamic window minimization -->
        <div
            class="border-l border-blue-300 flex flex-col bg-white bg-gradient-to-r from-blue-50 via-white to-gray-100"
            v-bind:class="{ 'hidden': isMinimized }"
            @click="selfAcknowledged();"
        >
            <!-- <background /> -->

            <!-- Chat Window Body -->
            <div class="window-h">
                <messages-block
                    :messages="messages"
                    :isMinimized="isMinimized"
                    :chatGroup="chatGroup"
                    :groupSeenStates="groupSeenStates"
                    :newSeenState="newSeenState"
                    :receivedMessage="receivedMessage"
                />
            </div>
            <!-- -------------------- -->


            <!-- Chat Window Footer -->
            <message-input
                @messageSent="getMessages()"
                :group_id="this.chatGroup.group.id"
            />
            <!-- -------------------- -->
        </div>
    </div>
</template>

<script>
import ChatParticipants from "./ChatParticipants.vue";
import WindowManagement from "./WindowManagement.vue";
import MessagesBlock from "./MessagesBlock";
import MessageInput from "./Footer/MessageInput.vue";
import Background from "./ChatBody/Background.vue";

import * as helpers from "../../../helpers/helpers_exporter.js";

import { mapGetters } from 'vuex';

export default {
    props:[
        'chatGroup',
        'windowIndex',
    ],

    data(){
        return {
            messages: [],
            isMinimized: false,
            lastMessageId: null,
            lastSenderId: null,
            receivedMessage: {},
            lastAcknowledgedMessageId: null,
            groupSeenStates: {},
            newSeenState: {}
        }
    },

    components: {
        'chat-participants': ChatParticipants,
        'messages-block': MessagesBlock,
        'message-input': MessageInput,
        'window-management': WindowManagement,
        'background': Background,
    },

    created() {
        this.connect();
        this.listenForMessagesSeen();
    },

    computed: {
        ...mapGetters({ user: "StateUser" }),

    },

    methods: {   
        minimizeWindow()
        {
            this.isMinimized = !this.isMinimized;
        },

        /**
         * If there are messages in chat group, get only those which have greater ID than "this.lastMessageId" 
         *      then merge existing messages and received ones into 'this.messages' variable
         * else 
         *      there are no messages, get all messsages 
         * 
         */
        getMessages()
        {
            // console.log(this.lastMessageId);
            // if(this.lastMessageId){
            //     console.log('this chat window already has messages, do "missing messages" get request');
            //     axios.get('/api/chat/group/' + this.chatGroup.group.id + '/from-msg/' + this.lastMessageId)
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
            //          helpers.createHashMap_OneToOne(res.data, 'id);
                     
            //     })
            //     .catch(error => {
            //         // 
            //     });
            // } else {
            //     // axios.get('/api/chat/group/' + this.chatGroup.group.id + '/messages')
            //     // .then(res => {
            //     //     this.messages = res.data;

            //     // })
            //     // .catch(error => {
            //     //     // 
            //     // });
            // }

            axios.get('/api/chat/group/' + this.chatGroup.group.id + '/messages')
            .then(res => {
                this.messages = Object.assign({}, helpers.createHashMap_OneToOne(res.data.messages, 'id') );
                this.setLatestMessageId( this.findLatestMessageId(this.messages) );
                this.setlastSenderId(this.findLatestSenderId(this.messages, this.lastMessageId));
                this.setReceivedMessage();
                // this.groupSeenStates = Object.assign({}, helpers.createHashMap_OneToMany(res.data.seen_states, 'last_message_seen_id', 'user_id') );
                this.groupSeenStates = res.data.seen_states;
                this.messages = Object.assign({}, this.resetAllSeenStatesOnMessages(this.messages));
            })
            .catch(error => {
                // 
            });

        },

        connect()
        {
            this.getMessages();

            Echo.private("group." + this.chatGroup.group.id)
            .listen('.message.new', e => {
                // There is a new message in this chat group,
                this.getMessages();
            });
        },

        // Event when user clicks his own window (he saw/red all messages)
        selfAcknowledged()
        {
            // Preventing user from acknowledging chat group with no messages.
            if(Object.keys(this.messages).length == 0){
                return;
            }
            
            // @BUG After new message is received, this.lastMessageId apparently is not what its supposed to be
            // Preventing user from acknowledging his own message.
            if(this.isUserOwnerOfLastMessage(this.messages[this.findLatestMessageId(this.messages)], this.user.id)){
                return;
            } 

            // Preventing user from acknowledging same message many times.
            if(this.lastMessageId == this.lastAcknowledgedMessageId) {
                return;
            } 
            
            
            this.setLatestAcknowledgedMessageId(this.lastMessageId);

            // tell GroupList component you have opened AND SEEN messages in this group
            this.$emit('groupAcknowledged', this.chatGroup.group.id);

            axios.post('/api/chat/message/seen', {
                'group_id': this.chatGroup.group.id,
                'lastMessageId': this.lastMessageId,
            });

        },

        listenForMessagesSeen()
        {
            Echo.private("group." + this.chatGroup.group.id)
            .listen('.message.seen', e => {
                // Reason formateData exists is that 'selfId' from DB is bad name, after 'selfId' is changed wherever it is being created,  
                // delete this var and just pass 'event' var
                const formatedData = {
                    group_id: e.seenData.group_id,
                    user_id: e.seenData.user_id,
                    last_message_seen_id: e.seenData.lastMessageId,
                }
                this.newSeenState = formatedData;
                
                // update existing state
                // let updated = helpers.updateHashMap_OneToMany(this.groupSeenStates, formatedData, formatedData.last_message_seen_id, formatedData.user_id, 'user_id');
                // this.groupSeenStates = Object.assign({}, updated);
                // console.log(this.groupSeenStates);
                // console.log(this.groupSeenStates);

                // // Clean null properties - Does this even work???
                // let cleaned = helpers.cleanup_oneToOne(this.groupSeenStates);
                // this.groupSeenStates = Object.assign({}, cleaned);

            });
        },

        setReceivedMessage()
        {
            this.receivedMessage = {
                user_id: this.lastSenderId,
                message_id: this.lastMessageId
            }
        },

        /**
         * Set lastest message in this chat window
         * 
         * Each time message is received this method must be updated.
         */
        setLatestMessageId(id)
        {
            this.lastMessageId = id; 
        },

        setlastSenderId(id)
        {
            this.lastSenderId = id;
        },

        setLatestAcknowledgedMessageId(id)
        {
            this.lastAcknowledgedMessageId = id; 
        },

        /**
         * Find latest message this chat window has.
         * 
         * This value might be different then one in database. Purpose of having this value is to only 'get request' messages which are 
         * not already in this chat window.   
         */
        findLatestMessageId(messages)
        {
            const messagesIds = Object.keys(messages);
            return messagesIds[messagesIds.length -1];
        },

        findLatestSenderId(messages, lastMessageId)
        {
            return messages[lastMessageId].user_id;
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
