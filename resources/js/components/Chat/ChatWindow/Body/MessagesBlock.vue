<template>
    <div class="h-full"
        :class="{
            'bg-gray-50': !group.hasUnseenState,
            'bg-green-50': group.hasUnseenState,
        }"
    >
        <vue-scroll 
            @handle-scroll="handleScroll"
            :ops="ops"
            :ref="this.refsNames.scroll"
        >
            <div class="flex flex-col-reverse" :ref="this.refsNames.block">
                <participants-typing
                    :group_id="group.id"
                    @typing="adjustScrollIfBottom"
                />
                <div class="mx-2" >
                    <div v-for="(message, messageId) in group.messages" :key="messageId" >
                        <one-message
                            :message="message"
                        />
                        <messages-seen 
                            :message="message"
                            :participants="group.participants"
                            :last_msg_id="group.last_msg_id"
                            :group_id="group.id"
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
            ops: {
                vuescroll: {},
                scrollPanel: {
                    initialScrollY: 1000000000000, // i need percent here and it doesnt work 
                    scrollingX: false,
                },
                rail: {},
                bar: {
                    keepShow: true
                }
            },

            refsNames: {
                scroll: 'messages-scroll',
                block: 'msgesBlocks'
            },

            config:{
                constHeightUnknown: 16, 
                autoScrollsToBottomOffset: 600,
                scrollDownOnNewMessage: true,
                awaitsEarlyMessages: false,
                scrollAdjustmentOffset: 200,
            }

        }
    },

    computed: {
        ...mapGetters({ user: "StateUser" }),
    },

    mounted() {
        
    },

    watch: {
        'group.last_msg_id': function(newVal, oldVal){
            this.$nextTick(() => {
                if(this.passedHeigthThreshold()) this.scrollDown()
            })
        }
    }, 

    methods: 
    {
        scrollDown() { this.$refs[this.refsNames.scroll].scrollTo({  y: "100%" },  300) },

        scrollDownSlow() { this.$refs[this.refsNames.scroll].scrollTo({  y: "200%" },  1000) },

        /**
         * Called every time user scolls and on mount
         * if user scrolled to top of chat indow AND isnt waiting for api to finish, 
         */
        handleScroll(vertical, horizontal, nativeEvent)
        {
            // if user scrolled to top of chat indow AND isnt waiting for api to finish, 
            if(vertical.scrollTop < 200 && !this.config.awaitsEarlyMessages){
                // set awaiting to true to prevent excessive api calls
                this.config.awaitsEarlyMessages = true

                this.$store.dispatch('groups/getEarliestMessages', {group_id: this.group.id}).then(()=>{
                    // Earliest messages received, allow this request to be executed if user scroll to top of window again
                    this.config.awaitsEarlyMessages = false
                })
            }
        },

        /**
         * config.constHeightUnknown value was calculated :
         * bottom - totalHeight = 16, for some reason, its constant for all chat windows
         */
        passedHeigthThreshold()
        { 
            if(!this.config.scrollDownOnNewMessage) 
                return

            const bottom = this.getLowestPointUserSeesInMessagesBlock()
            const totalHeight = this.getCurrentMessagesHeight()
            
            return totalHeight - bottom < this.config.autoScrollsToBottomOffset 
                ? true 
                : false 
        },

        getCurrentMessagesHeight() 
        { 
            return this.$refs[this.refsNames.block].clientHeight + this.config.constHeightUnknown
        },

        getLowestPointUserSeesInMessagesBlock()
        {
            return this.$refs[this.refsNames.scroll].getPosition().scrollTop + this.$refs[this.refsNames.scroll].$el.clientHeight
        },

        adjustScrollIfBottom()
        {
            const bottom = this.getLowestPointUserSeesInMessagesBlock()
            const totalHeight = this.getCurrentMessagesHeight()

            if(totalHeight - bottom < this.config.scrollAdjustmentOffset){
                this.$nextTick(() => {
                    this.scrollDownSlow()
                })
            }
                
        }
    },

}

// this.$refs[this.refsNames.scroll].getScrollProcess().v > 0.5

</script>

<style>
.inner-cw-shadow-seen {
    box-shadow: 0px 0px 5px 2px rgb(0 134 255 / 34%) inset;
}

.inner-cw-shadow-unseen {
    box-shadow: 0px 0px 5px 2px rgb(0 255 152 / 34%) inset;
}

</style>
