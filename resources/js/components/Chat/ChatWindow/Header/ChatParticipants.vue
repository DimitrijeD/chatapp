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
         * Private group always shows other user in this group.
         * 
         */
        whatToShowInHeader()
        {
            if(this.group.model_type == "PRIVATE"){
                this.showComponent = 'list-chat-participants'
                return
            }

            if(this.group.participants.length <= 3){
                this.showComponent = 'list-chat-participants'
                return
            }

            if(!this.group.name){
                this.showComponent = 'list-chat-participants'
                return
            }

            this.showComponent = 'group-name' 
        }
    },
}
</script>

<style scoped>
.nowrap {
    white-space: nowrap ;
}
</style>