<template>
    <div class="flex justify-end mx-4 my-1 flex-wrap">
        <div 
            v-for="participant in participants" 
            :key="participant.id" 
        >
            <div v-if="showParticipantAsSeen(participant)">
                <img
                    :src="participant.thumbnail"
                    alt="no img :/"
                    class="w-8 h-8 object-cover border-2 border-gray-200 rounded-full"
                    :class="{
                        'shadow-small-img-self':   isOwnerOfThisMessage(participant),
                        'shadow-small-img-other': !isOwnerOfThisMessage(participant),
                    }"
                >
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from "vuex";

export default {
    props:[
        'message', 'participants', 'group_id'
    ],

    data(){
        return{
            
        }
    },

    computed: {
        ...mapGetters({ 
            user: "StateUser",
        }),

        last_msg_id() {
            return this.$store.getters['groups/getGroupLastMsgId']({group_id: this.group_id})
        },
    },

    created(){
        
    },

    methods: 
    {
        showParticipantAsSeen(participant)
        {
            if(!this.isThisLastSeenMessage(participant)) return false

            if(this.isOwnerOfThisMessage(participant)) return false

            if(this.isParticipantOwnerOfLastMessage(participant)) return false

            if(this.hasMessagesAfterSeen(participant)) return false

            return true
        },

        isThisLastSeenMessage(participant)
        {
            return participant.pivot.last_message_seen_id == this.message.id 
                ? true 
                : false
        },

        isOwnerOfThisMessage(participant)
        {
            return participant.id == this.message.user_id 
                ? true 
                : false
        },
        
        isParticipantOwnerOfLastMessage(participant)
        {
            const lastMsgOwnerId = this.$store.getters['groups/getOwnerIdOfLastMessage']({
                group_id: this.group_id,
                last_msg_id: this.last_msg_id
            })

            return lastMsgOwnerId == participant.id
                ? true
                : false
        },

        // if there are messages, after "participant.pivot.last_message_seen_id" which belong to him, do not show that user here
        hasMessagesAfterSeen(participant)
        {
            return false // TODO
        }

    }

}
</script>

<style>
.shadow-small-img-self {
    box-shadow: 0px 0px 5px 2px rgb(0 134 255 / 54%);
}

.shadow-small-img-other {
    box-shadow: 0px 0px 5px 2px rgb(0 134 255 / 54%);
}
</style>