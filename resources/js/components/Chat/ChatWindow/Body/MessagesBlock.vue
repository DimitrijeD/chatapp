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
                
                <div> <!-- This div is required to leave flex-col-reverse class -->
                    <div v-for="(block, index) in blocks" :key="index">
                        <same-user-message-block 
                            :block="block"
                            :group="group"
                        />
                    </div>
                </div>
            </div>
        </vue-scroll>
    </div>
</template>

<script>
import ParticipantsTyping from "./ParticipantsTyping.vue";
import SameUserMessageBlock from './SameUserMessageBlock.vue';
import { mapGetters } from "vuex";
import * as ns from '../../../../store/module_namespaces.js'

export default {
    props: [
        'group',
    ],

    components: {
        'participants-typing': ParticipantsTyping,
        'same-user-message-block': SameUserMessageBlock,
    },

    data() {
        return {
            ops: {
                scrollPanel: {
                    initialScrollY: 1000000000000, // i need percent here and it doesnt work 
                    scrollingX: false,
                },
                bar: { keepShow: true }
            },

            refsNames: {
                scroll: 'messages-scroll',
                block: 'msgesBlocks',
            },

            config:{
                constHeightUnknown: 16, 
                autoScrollsToBottomOffset: 600,
                scrollDownOnNewMessage: true,
                awaitsEarlyMessages: false,
                scrollAdjustmentOffset: 200,
            },
            gm_ns: ns.groupModule(this.group.id), 

            blocks: []
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
        'group.messages_tracker.last_message.id': function (newVal, oldVal) {
            this.$nextTick(() => {
                if(this.passedHeigthThreshold()) this.scrollDown()
            })
        },

        'group.messages': {
            handler: function (newVal, oldVal) {
                this.createBlocks()
            },
            deep: true,
        },

    }, 

    methods: 
    {
        scrollDown() { this.$refs[this.refsNames.scroll].scrollTo({  y: "100%" },  300) },

        scrollDownSlow() { this.$refs[this.refsNames.scroll].scrollTo({  y: "200%" },  1000) },

        /**
         * Called every time user scolls and on mount
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
                
        },

        createBlocks()
        {
            this.blocks = []
            
            if(this.group.messages == {}) return 

            const msgIds = Object.keys( this.group.messages).map(id => {
                return Number(id);
            }).sort((a, b) => {
                return a - b;
            })

            let blockCollector = this.getFreshBlockCollector()

            let blockOwnerId = this.group.messages[msgIds[0]].user_id

            for(let i in msgIds){
                if(this.group.messages[msgIds[i]].user_id == blockOwnerId){
                    blockCollector.messages.push(this.group.messages[msgIds[i]])
                    blockCollector.blockOwnerId = this.group.messages[msgIds[i]].user_id
                } else {
                    this.blocks.push(blockCollector)
                    blockCollector = this.getFreshBlockCollector()
                    blockCollector.messages.push(this.group.messages[msgIds[i]])
                    blockOwnerId = this.group.messages[msgIds[i]].user_id
                    blockCollector.blockOwnerId = this.group.messages[msgIds[i]].user_id
                }
            }

            // add last collected block if not empty
            if(blockCollector.messages.length != 0) this.blocks.push(blockCollector)

        },

        getFreshBlockCollector(){
            return {
                messages: [],
                blockOwnerId: null
            }
        },


    },

}

</script>

<style>

</style>
