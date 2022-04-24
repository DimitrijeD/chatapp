<template>
    <div v-show="hasAnyPeopleSeen" class="grid grid-cols-10">
        <div v-for="participant in this.participants" :key="participant.id" class="">
            <div v-if="isThisLastSeenMessage(participant)" class="">
                <img
                    :src="participant.thumbnail"
                    alt="no img :/"
                    class="w-10 h-10 object-cover border-2 border-gray-200 rounded-full"
                >
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from "vuex";

export default {
    props:[
        'msgId',
        'participants',
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
            let p = participant.pivot 
            if(p.last_message_seen_id == this.msgId && this.user.id != p.user_id ){
                return true
            }
            return false
        },
    }


}
</script>

<style scoped>

</style>