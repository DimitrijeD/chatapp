<template>
    <div class="ml-2 mr-2 mb-2 rounded-2xl" 
        :class="{
            'bg-blue-100 shadow-blue border-blue': !isSelf,
            'bg-gray-200 shadow-gray border-gray': isSelf,
        }"
    >
        <small-user 
            :user="getUser(this.block.blockOwnerId)"
            :userNameCls="'text-gray-600 text-base'"
            :imgCls="'w-12 h-12'"
            class="ml-2"
        /> 

        <!-- List of messages in this block -->
        <div class="">
            <div 
                v-for="message in block.messages" :key="message.id"
                class="pl-2 pr-2 pb-2 rounded-2xl "
                :class="{
                    'hover:bg-blue-200 border hover:border-blue-300': !isSelf,
                    'hover:bg-gray-300 border hover:border-gray-400': isSelf,
                    'bg-green-200': msgNotSeen(message.id),
                }"
            >
                <div class="text-base static-border">
                    <!-- Need delete message component here with SVG icon -->
                    <!-- <span class="float-right text-sm rounded-full border border-gray-200 cursor-pointer border hover:border-red-300">X</span> -->

                    <!-- Message Content -->
                    <p style="white-space: pre;" class="font-serif mt-0.5 msg-txt text-gray-700 font-sans tracking-wide break-words" v-html="message.text">
                    </p>
                    <!-- / -->

                    <vue-moments-ago
                        class="text-gray-500 flex flex-row-reverse"
                        prefix=""
                        suffix="ago"
                        :date="message.updated_at"
                        lang="en"
                    />
                    <div v-if="group.whoSawWhat[message.id]">
                        <messages-seen 
                            :message_id="message.id"
                            :group_id="group.id"
                            :message="message"
                            :user_ids="group.whoSawWhat[message.id]"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from "vuex";
import * as ns from '../../../../store/module_namespaces.js'
import VueMomentsAgo from 'vue-moments-ago'
import SmallUser from '../../reuseables/SmallUser.vue';
import MessagesSeen from './MessagesSeen.vue';

export default {
    props: [
        "block", "group"
    ],

    components:{
        'vue-moments-ago': VueMomentsAgo,
        'small-user': SmallUser,
        'messages-seen': MessagesSeen,
    },

    data(){
        return {
            gm_ns: ns.groupModule(this.group.id),
            refsNames: {
                scroll: 'messages-scroll',
                block: 'msgesBlocks',
                seen: 'msgesSeen-'
            },
        }
    },

    computed:{
        ...mapGetters({ 
            user: "user",
        }),

        blockOwner(){ return this.$store.getters[this.gm_ns + '/getParticipant'](this.block.blockOwnerId) },
        
        isSelf(){ return this.block.blockOwnerId == this.user.id },

    },

    created(){
        
    },

    mounted(){
        
    },

    methods: {
        getUser(id) { return this.$store.getters[this.gm_ns + '/getParticipant'](id) },

        getMessageSeenBlockRef(msg_id){ return this.refsNames.seen + msg_id },

        msgNotSeen(msg_id){
            if(this.group.messages_tracker.seen) return false

            if(this.group.participants[this.user.id].pivot.last_message_seen_id < msg_id) return true

            return false
        },
    }


}
</script>

<style>


.shadow-blue {
    -webkit-box-shadow: -5px 8px 10px -6px rgb(0 74 149 / 56%);
    -moz-box-shadow: -5px 8px 10px -6px rgb(0 74 149 / 56%);
            box-shadow: -5px 8px 10px -6px rgb(0 74 149 / 56%);
}

.border-blue{
    border: 1px solid rgb(0 74 149 / 26%);
}

.border-gray{
    border: 1px solid rgb(45 35 35 / 26%);
}

.static-border{
    border-top: 1px solid rgb(0 74 149 / 10%);
}

</style>