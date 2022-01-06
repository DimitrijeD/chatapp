<template>
    <div class="flex flex-col-reverse bg-white h-96 overflow-y-scroll"
         :class="{ 'hidden': isMinimized }"
    >
        <participants-typing
            :groupId="this.chatGroup.group.id"
        />

        <div v-for="message in messages">
            <one-message
                :message="message"
                :userSelf="userSelf"
                :key="message.id"
            />

            <!-- if there are any users who saw this message last time -->
            <div v-if="message.seen_state.length > 0" class="flex justify-end">
                <div v-for="seenState in message.seen_state">
                    <!--
                    On logged in user, do not show himself as seen AND do not show user if hes sender
                    so if I sends a message to "user2" and I click on window, on "user2" window I wont appear as seen below my own message,
                    that's pointless
                    Second if also removes all appearance of 'seen' for user whose last message belongs to
                    -->
                    <div v-if="seenState.id != userSelf.id && messages[0].user.id != seenState.id">
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


export default {
    props: [
        'messages',
        'userSelf',
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

    watch: {
        newSeenEvent: {
            handler: function(newValue, oldValue) {
                let oldStateData = this.findUsersState(this.messages, newValue.selfId);

                let tempState = this.messages[oldStateData.msgIndex].seen_state[oldStateData.userIndexInState];
                // console.log(tempState);

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
