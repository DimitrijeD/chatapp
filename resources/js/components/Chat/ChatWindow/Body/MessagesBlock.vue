<template>
    <div class="flex flex-col-reverse h-full overflow-y-scroll relative">
        <participants-typing
            :group_id="this.group.id"
        />
        <div class="ml-2 mr-1.5">
            <div v-for="(message, messageId) in group.messages" :key="messageId">
                <one-message
                    :message="message"
                />
                <messages-seen 
                    v-if="false"
                    :msgId="messageId"
                    :participants="group.participants"
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
        'group',
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

    computed: {
        ...mapGetters({ user: "StateUser" }),
    },

    mounted() {

    },

    watch: {

    },

    methods: {

    },

}
</script>

<style>
.msg-block-height{
    height: 430px;
}

</style>
