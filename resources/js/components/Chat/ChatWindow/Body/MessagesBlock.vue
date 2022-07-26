<template>
    <div class="h-full"
        :class="{
            'bg-gray-50':   this.seen,
            'bg-green-50': !this.seen,
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
                            :ref="getMessageSeenBlockRef(messageId)"
                            :message_id="messageId"
                            :group_id="group.id"
                            :message="message"
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
import * as ns from '../../../../store/module_namespaces.js'

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
                block: 'msgesBlocks',
                seen: 'msgesSeen-'
            },

            config:{
                constHeightUnknown: 16, 
                autoScrollsToBottomOffset: 600,
                scrollDownOnNewMessage: true,
                awaitsEarlyMessages: false,
                scrollAdjustmentOffset: 200,
            },
            gm_ns: ns.groupModule(this.group.id), // group module name space
        }
    },

    computed: {
        ...mapGetters({ 
            user: "user",
         }),

        seen(){ 
            return this.$store.getters[`${this.gm_ns}/seen`]
        },
    },

    created(){
        
    },

    mounted() {
        
    },

    watch: {
        'group.messages_tracker.last_message.id': function(newVal, oldVal){
            this.$nextTick(() => {
                if(this.passedHeigthThreshold()) this.scrollDown()
            })
        },

        'group.whoSawWhat': {
            handler: function (newVal, oldVal) {
                this.resetSeenStates(oldVal)
                this.injectSeenStates(newVal)
            },
            deep: true,
        }

    }, 

    methods: 
    {
        resetSeenStates(oldUsersIds){
            for(let msg_id in oldUsersIds){
                this.$refs[this.getMessageSeenBlockRef(msg_id)][0].resetUserIds()
            }
        },

        injectSeenStates(newUsersIds){
            for(let msg_id in newUsersIds){
                if( !this.$refs[this.getMessageSeenBlockRef(msg_id)] ){ 
                    console.log('msg seen ref doesnt exist. Most likely message isnt fetched (its old message)')
                    // he i can have some kind of handler for this case
                    // each time early messages are called, this function should be run
                    continue
                }
            
                if( !Array.isArray( this.$refs[this.getMessageSeenBlockRef(msg_id)] )){ 
                    console.log('msg seen refs are no longer arrays.. what?')
                    continue
                }

                if( this.$refs[this.getMessageSeenBlockRef(msg_id)].length > 1 ){
                    console.log('MessagesBlock group.whoSawWhat watcher, msg seen  refs contain more than one element, wat is going on?')
                    continue
                }

                this.$refs[this.getMessageSeenBlockRef(msg_id)][0].setUserIds(newUsersIds[msg_id])
            }
        },

        getMessageSeenBlockRef(msg_id){ return this.refsNames.seen + msg_id },

        scrollDown() { this.$refs[this.refsNames.scroll].scrollTo({  y: "100%" },  300) },

        scrollDownSlow() { this.$refs[this.refsNames.scroll].scrollTo({  y: "200%" },  1000) },

        /**
         * Called every time user scolls and on mount
         * if user scrolled to top of chat indow AND isnt waiting for api to finish, 
         */
        handleScroll(vertical, horizontal, nativeEvent){
            // if user scrolled to top of chat indow AND isnt waiting for api to finish, 
            if(vertical.scrollTop < 200 && !this.config.awaitsEarlyMessages){
                // set awaiting to true to prevent excessive api calls
                this.config.awaitsEarlyMessages = true

                this.$store.dispatch(this.gm_ns + '/getEarliestMessages').then(()=>{
                    // Earliest messages received, allow this request to be executed if user scroll to top of window again
                    this.config.awaitsEarlyMessages = false
                })
            }
        },

        /**
         * config.constHeightUnknown value was calculated :
         * bottom - totalHeight = 16, for some reason, its constant for all chat windows
         */
        passedHeigthThreshold(){ 
            if(!this.config.scrollDownOnNewMessage) return

            const bottom = this.getLowestPointUserSeesInMessagesBlock()
            const totalHeight = this.getCurrentMessagesHeight()
            
            return totalHeight - bottom < this.config.autoScrollsToBottomOffset 
                ? true 
                : false 
        },

        getCurrentMessagesHeight(){ 
            return this.$refs[this.refsNames.block].clientHeight + this.config.constHeightUnknown
        },

        getLowestPointUserSeesInMessagesBlock(){
            return this.$refs[this.refsNames.scroll].getPosition().scrollTop + this.$refs[this.refsNames.scroll].$el.clientHeight
        },

        adjustScrollIfBottom(){
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

</script>

<style>

</style>
