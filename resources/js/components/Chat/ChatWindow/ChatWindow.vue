<template>
    <!-- Entire chat window wrapper -->
    <div class="w-96 mr-1 flex flex-col"
        :class="{ 'flex-col-reverse': isMinimized }"
    >
        <!-- chat window header -->
        <div class="flex w-96 h-12 bg-blue-500">
            <chat-participants
                :participants="chatGroup.participants"
                :userSelf="userSelf"
            />
            <window-management
                :windowIndex="windowIndex"
                v-on="$listeners"
                @minimizeWindow = "minimizeWindow()"
            />
        </div>

        <!-- chat window body -->
        <!-- wrapper for dynamic window minimization -->
        <div
            class="border-l border-blue-300 flex flex-col"
            v-bind:class="{ 'hidden': isMinimized }"
            @click="selfAcknowledged();"
        >
            <!-- @todo On mount, it's getting all messages of this chat group.
            After reducing number of messages per scrollUp, this must be array of blocks in stead of one block component
            And 'who has seen what message' should be injected into right messages-block component
            -->
            <messages-block
                :messages="messages"
                :userSelf="userSelf"
                :isMinimized="isMinimized"
                :chatGroup="chatGroup"
                :newSeenEvent="newSeenEvent"
            />


            <!-- chat window footer -->
            <message-input
                @messageSent="getMessages()"
                :groupId="this.chatGroup.group.id"
                :userSelf="userSelf"
            />
        </div>
    </div>
</template>

<script>
import ChatParticipants from "./ChatParticipants.vue";
import WindowManagement from "./WindowManagement.vue";
import MessagesBlock from "./MessagesBlock";
import MessageInput from "./MessageInput.vue";
import ParticipantsTyping from "./ParticipantsTyping.vue";

export default {
    props:[
        'chatGroup',
        'userSelf',
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
        'participants-typing': ParticipantsTyping,
    },

    created() {
        this.connect();
        this.listenForMessagesSeen_v2();
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
                console.log(error);
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

        // Event when user clicks his own window:
        selfAcknowledged()
        {
            // if user is owner of last message in chat, there is noting to 'acknowledge'
            if(this.messages[0].user.id === this.userSelf.id){
                return;
            }

            // tell GroupList component you have opened AND SEEN messages in this group
            this.$emit('groupAcknowledged', this.chatGroup.group.id);

            axios.post('/api/chat/messages-seen', {
                'groupId': this.chatGroup.group.id,
                'lastMessageId': this.messages[0].id,
                'selfId': this.userSelf.id
            });

        },

        listenForMessagesSeen_v2()
        {
            Echo.private("group." + this.chatGroup.group.id)
            .listen('.message.seen', e => {
                this.newSeenEvent = e.seenData;
                // console.log(e.seenData);
            });
        },

        findMessageById(messages, messageId)
        {
            for (var index in messages){
                if (messages.hasOwnProperty(index) && messages[index].id === messageId) {
                    return index;
                }
            }
        },

    }
}

</script>

<style>
.h-112{
    height: 28rem;
}
</style>
