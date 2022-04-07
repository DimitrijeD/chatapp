<template>
    <div class="flex flex-col-reverse h-full overflow-y-scroll relative">
        <participants-typing
            :group_id="this.chatGroup.group.id"
            :receivedMessage="receivedMessage"
        />
        <div class="z-50">
            <div v-for="(message, messageId) in messages" :key="messageId">
                <one-message
                    :message="message"
                    :key="messageId"
                />
                <messages-seen 
                    :seenStates="groupSeenStates"
                    :msgId="messageId"
                />
            </div>
        </div>
    </div>
</template>

<script>
import OneMessage from './OneMessage.vue';
import MessagesSeen from './MessagesSeen.vue';
import ParticipantsTyping from "./ParticipantsTyping.vue";

import { mapGetters } from "vuex";

export default {
    props: [
        'messages',
        'isMinimized',
        'chatGroup',
        'groupSeenStates',
        'newSeenState',
        'receivedMessage'
    ],

    components: {
        'one-message': OneMessage,
        'messages-seen': MessagesSeen,
        'participants-typing': ParticipantsTyping,
    },

    data() {
        return {
           
        }
    },

    mounted() {

    },

    watch: {
        newSeenState: {
            handler: function(newValue, oldValue) {
                this.newSeenStateEvent(newValue);
            },
            deep: true
        }
    },

    computed: {
        ...mapGetters({ user: "StateUser" }),
    },


    methods: {
        newSeenStateEvent(newSeen)
        {
            this.cleanupStates(newSeen);
            // console.log('newValue: ', newSeen);
            // console.log('groupSeenStates: ', this.groupSeenStates);
        },

        cleanupStates(newSeen)
        {
            // first remove existing seen 
            for (let index in this.groupSeenStates){
                if(this.groupSeenStates[index]['user_id'] == newSeen.user_id){
                    this.groupSeenStates.splice(index, 1);
                }
            }

            // add element 
            this.groupSeenStates.push(newSeen);

        },

        createSeenID(messageId)
        {
            return 'msg_id_' + messageId;
        },

    },

}
</script>

<style>
.msg-block-height{
    height: 430px;
}
</style>
