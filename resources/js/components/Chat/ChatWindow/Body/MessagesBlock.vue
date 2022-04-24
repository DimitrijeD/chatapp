<template>
    <div class="window-h window-w">
        <vue-scroll 
            @handle-resize="handleResize" 
            :ops="ops"
            ref="messages-scroll"
        >
            <div class="flex flex-col-reverse" ref="msgesBox">
                <participants-typing
                    :group_id="this.group.id"
                />
                <div class="ml-2 mr-1.5">
                    <div v-for="(message, messageId) in group.messages" :key="messageId">
                        <one-message
                            :message="message"
                        />
                        <messages-seen 
                            :msgId="messageId"
                            :participants="group.participants"
                        />
                    </div>
                </div>
            </div>
        </vue-scroll>
    </div>
</template>

<script>
import OneMessage from './OneMessage.vue';
import MessagesSeen from './MessagesSeen.vue';
import ParticipantsTyping from "./ParticipantsTyping.vue";
// import vuescroll from 'vuescroll';
import { mapGetters } from "vuex";

export default {
    props: [
        'group',
    ],

    components: {
        'one-message': OneMessage,
        'messages-seen': MessagesSeen,
        'participants-typing': ParticipantsTyping,
        // 'vue-scroll': vuescroll,
    },

    data() {
        return {
            ops: {
                vuescroll: {

                },
                scrollPanel: {
                    initialScrollY: 1000000000000, // i need percent here and it doesnt work 
                    scrollingX: false,
                },
                rail: {},
                bar: {
                    keepShow: true
                }
            },

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
        handleResize(){
            // console.log('msges h', this.$refs.msgesBox.clientHeight)
            // console.log('scrl pos', this.$refs["messages-scroll"].getPosition())
            this.$refs["messages-scroll"].scrollTo({
                y: "100%" // when this occurs, move scroll to current percent of scroll    
            },
                300
            );
        }
    },

}
</script>

<style>
.msg-block-height{
    height: 430px;
}

</style>
