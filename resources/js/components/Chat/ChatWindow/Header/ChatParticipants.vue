<template>
    <div class="w-full m-auto z-50 truncate">
        <div class=" ml-2">
            <list-chat-participants 
                v-if="showComponent == 'list-chat-participants'"
                :group="group"
            />
            
            <group-name 
                v-if="showComponent == 'group-name'"
                :group="group"
            />

            <div class="header-text" v-if="showComponent == 'default-show'">{{ defaultText }}</div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from "vuex";
import ListChatParticipants from "./ChatParticipants/ListChatParticipants.vue";
import GroupName from "./ChatParticipants/GroupName.vue";

export default {
    props:[
        'group',
    ],

    components: {
        'list-chat-participants': ListChatParticipants,
        'group-name': GroupName,
    },

    data(){
        return{
            showComponent: null,
            defaultText: "A Quiet Place"
        }
    },

    created() {
        this.whatToShowInHeader()
    },

    mounted() {
    
    },

    computed: {
        ...mapGetters({ user: "StateUser" }),
    },

    methods:{
        /**
         * Determine what content should be displayed in Chat Window Header
         * 
         * List of users or group name.
         */
        whatToShowInHeader()
        {
            if(this.group.model_type == "PRIVATE"){
                this.showComponent = 'list-chat-participants'
                return
            }

            if(!this.group.name && this.group.participants.length == 1){
                this.showComponent = 'default-show'
                return
            }

            if(!this.group.name && this.group.participants.length > 1){
                this.showComponent = 'list-chat-participants'
                return
            }

            if(this.group.name){
                this.showComponent = 'group-name'
                return
            } 

        }
    },
}
</script>

<style>
.nowrap {
    white-space: nowrap ;
}

.header-text {
    @apply text-base text-white font-semibold ml-2;
}
</style>