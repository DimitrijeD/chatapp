<template>
    <div>
        <div v-for="seenState in seenStates" :key="seenState.user_id">
            <div 
                v-if="asd(seenState)"
                class="ml-1.5 mr-1.5 "
            >
                <span class="text-blue-400 text-sm hover:text-blue-500">
                    {{ seenState.user_id }} seen
                </span>
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from "vuex";

export default {
    props:[
        'seenStates',
        'msgId'
    ],

    computed: {
        ...mapGetters({ user: "StateUser" }),
    },

    methods: {
        asd(seenState)
        {
            if(seenState.last_message_seen_id ==  this.msgId && this.user.id != seenState.user_id){
                return true;
            }
            return false;
        }
    }

}
</script>
