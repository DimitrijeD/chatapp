<template>
    <div 
        v-if="hasAnybodySeenThis()" 
        class="flex justify-end mx-4 my-1 flex-wrap"
    >
        <div v-for="participant_id in user_ids">
            <div v-if="toShow(participant_id)">
                <img
                    :src="getParticipantThumbnail(participant_id)"
                    class="w-8 h-8 object-cover border-2 border-gray-200 rounded-full"
                    :class="diffSelfCls(participant_id)"
                    alt=""
                >    
            </div>  
        </div>
    </div>
</template>

<script>
import { mapGetters } from "vuex";
import * as ns from '../../../../store/module_namespaces.js'

export default {
    props:[
        'group_id', 'message'
    ],

    data(){
        return{
            gm_ns: ns.groupModule(this.group_id),
            user_ids: [],

            classes: {
                self: 'shadow-small-img-self',
                notSelf: 'shadow-small-img-other'
            },

            config: {
                neverShowUserBewlowHisOwnMsg: true,
                neverShowSelf: true,
            }
        }
    },

    computed: {
        ...mapGetters({ 
            user: "user",
        }),

    },

    created(){
        
    },

    methods: 
    {
        setUserIds(user_ids){ this.user_ids = user_ids },

        getUserIds(){ return this.user_ids },

        resetUserIds(){ this.user_ids.splice(0) }, 

        hasAnybodySeenThis(){ return this.user_ids.length > 0 ? true : false },

        getParticipantThumbnail(id) { return this.$store.getters[this.gm_ns + '/getParticipantThumbnail'](id) },

        diffSelfCls(participant_id) { 
            return this.message.user_id == participant_id 
                ? this.classes.self 
                : this.classes.notSelf 
        },

        toShow(participant_id){
            if(this.config.neverShowUserBewlowHisOwnMsg){
                if(this.isMsgOwner(participant_id)) return false
            }

            if(this.config.neverShowSelf){
                if(this.isSelf(participant_id)) return false
            }

            return true
        },

        isMsgOwner(participant_id){
            return this.message.user_id == participant_id
        },

        isSelf(participant_id){
            return this.user.id == participant_id
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