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
            class="border-l border-blue-300 flex flex-col"
            v-bind:class="{ 'hidden': isMinimized }"
            @click="selfAcknowledged();"
        >

            <!-- Chat Window Body -->
            <div class="window-h window-body-bg-color">
            <!-- @todo On mount, it's getting all messages of this chat group.
            After reducing number of messages per scrollUp, this must be array of blocks in stead of one block component
            And 'who has seen what message' should be injected into right messages-block component
            -->
                <messages-block
                    :messages="messages"
                    :isMinimized="isMinimized"
                    :chatGroup="chatGroup"
                    :newSeenEvent="newSeenEvent"
                />
            </div>
            <!-- -------------------- -->


            <!-- Chat Window Footer -->
            <message-input
                @messageSent="getMessages()"
                :groupId="this.chatGroup.group.id"
            />
            <!-- -------------------- -->
        </div>
    </div>
</template>

<script>
import ChatParticipants from "./ChatParticipants.vue";
import WindowManagement from "./WindowManagement.vue";
import MessagesBlock from "./MessagesBlock";
import MessageInput from "./MessageInput.vue";

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
            newSeenEvent: {},
        }
    },

    components: {
        'chat-participants': ChatParticipants,
        'messages-block': MessagesBlock,
        'message-input': MessageInput,
        'window-management': WindowManagement,
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

        getMessages()
        {
            axios.get('/api/chat/group/' + this.chatGroup.group.id + '/get-all-messages')
            .then(res => {
                this.messages = res.data;
            })
            .catch(error => {
                // Tell user to refresh page or come back later ...
            })
        },

        connect()
        {
            this.getMessages();
            Echo.private("group." + this.chatGroup.group.id)
            .listen('.message.new', e => {
                this.getMessages();
            });
        },

        // @TODO Send seen message event only once per message
        // Event when user clicks his own window:
        selfAcknowledged()
        {
            // if there are no messages in chat
            // or if user is owner of last message in chat, there is noting to 'acknowledge'
            if(this.messages.length === 0 || this.messages[0].user.id === this.user.id){
                return;
            }
            // tell GroupList component you have opened AND SEEN messages in this group
            this.$emit('groupAcknowledged', this.chatGroup.group.id);

            axios.post('/api/chat/messages-seen', {
                'groupId': this.chatGroup.group.id,
                'lastMessageId': this.messages[0].id,
                'selfId': this.user.id
            });

        },

        listenForMessagesSeen()
        {
            Echo.private("group." + this.chatGroup.group.id)
            .listen('.message.seen', e => {
                this.newSeenEvent = e.seenData;
                
            });
        },

        findMessageById(messages, messageId)
        {
            for (let index in messages){
                if (messages.hasOwnProperty(index) && messages[index].id === messageId) {
                    return index;
                }
            }
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

.window-body-bg-color{
    background-color: rgb(252, 255, 255);;
}


</style>
