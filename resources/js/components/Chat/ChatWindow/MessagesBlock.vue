<template>
    <div class="flex flex-col-reverse h-full overflow-y-scroll"
         :class="{ 'hidden': isMinimized }"
    >
        <!-- Since message order is reversed by 'flex-col-reverse' this component must be
        on top of template in order to display participants typing on the bottom of the chat messages block/s -->
        <participants-typing
            :groupId="this.chatGroup.group.id"
        />

        <div v-for="message in messages" :key="message.id">
            <one-message
                :message="message"
                :key="message.id"
            />

            <!-- if there are any users who saw this message last time -->
            <div v-if="message.seen_state.length > 0" class="flex justify-end">
                <div v-for="seenState in message.seen_state" :key="seenState.id">
                    <!--
                    Do not show 'user' as seen AND do not show 'user' if he is sender
                    so if "user1" sends a message to "user2" and "user1" click on window, on "user2" window "user1" wont appear as seen below his own message,
                    that's pointless
                    Second condition removes all appearance of 'seen' for user whose last message belongs to
                    -->
                    <div v-if="seenState.id != user.id  &&  messages[0].user.id != seenState.id">
                        <messages-seen
                            :firstName="seenState.firstName"
                        />
                    </div>

                </div>
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
        'newSeenEvent'
    ],

    components: {
        'one-message': OneMessage,
        'messages-seen': MessagesSeen,
        'participants-typing': ParticipantsTyping,
    },

    data(){
        return {

        }
    },

    created(){

    },

    computed: {
        ...mapGetters({ user: "StateUser" }),

    },

    /**
     * @TODO Theres a bug, after new group is created and user1 sends a message to user2,
     * 'msgIndex' is undefined because message with index of '0' doesnt exist.
     * It would be awesoome if I refactored this completely because I have no idea what it does and how it does it
     */
    watch: {
        newSeenEvent: {
            handler: function(newValue, oldValue) {
                let oldStateData = this.findUsersState(this.messages, newValue.selfId);
                let tempState = this.messages[oldStateData.msgIndex].seen_state[oldStateData.userIndexInState];

                this.messages[oldStateData.msgIndex].seen_state.splice(oldStateData.userIndexInState, 1);
                this.messages[0].seen_state.push(tempState);

            },
            deep: true
        }
    },

    methods:
    {
        findUsersState(messages, userId)
        {
            for (let msgIndex in messages){
                if (messages.hasOwnProperty(msgIndex)){
                    for (let usrIndex in messages[msgIndex].seen_state){
                        let userStatesInMsg = messages[msgIndex].seen_state;
                        if (userStatesInMsg.hasOwnProperty(usrIndex) && userStatesInMsg[usrIndex].id === userId){
                            return {
                                msgIndex: msgIndex,
                                userIndexInState: usrIndex,
                            };
                        }
                    }
                }
            }
        },

    },

}
</script>

<style>
.msg-block-height{
    height: 430px;
}
</style>
