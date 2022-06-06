<template>
    <div v-show="hasAnyPeopleSeen" class="flex justify-end mx-4 my-1 flex-wrap">
        <div 
            v-for="participant in this.participants" 
            :key="participant.id" 
            class=""
        >
            <div 
                v-if="isThisLastSeenMessage(participant) && !isOwnerOfThisMessage(participant)" 
                class=""
            >
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
        'message', 'participants', 'last_msg_id', 'group_id'
    ],

    data(){
        return{
            hasAnyPeopleSeen: true,
        }
    },

    computed: {
        ...mapGetters({ user: "StateUser" }),
    },

    created(){

    },

    methods: 
    {
        isThisLastSeenMessage(participant)
        {
            if(participant.pivot.last_message_seen_id == this.message.id ){
                return true
            }
            return false
        },

        isOwnerOfThisMessage(participant)
        {
            return participant.id == this.message.user_id ? true : false
        },

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