<template>
    <div class="w-full m-auto z-50 truncate">
        <div class=" ml-2">
            <list-chat-participants 
                v-if="showComponent == 'list-chat-participants'"
                :participants="group.participants"
            />
            
            <group-name 
                v-if="showComponent == 'group-name'"
                :group_name="group.name"
            />

            <div class="header-text" v-if="showComponent == 'default-show'">{{ defaultText }}</div>
            <div class="header-text" v-if="showComponent == 'many-user-text'">{{ manyUserText }}</div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from "vuex";
import ListChatParticipants from "./ChatParticipants/ListChatParticipants.vue";
import GroupName from "./ChatParticipants/GroupName.vue";
import * as ns from '../../../../store/module_namespaces.js'

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
            defaultText: "A Quiet Place",
            gm_ns: ns.groupModule(this.group.id),
        }
    },

    created() {
        this.whatToShowInHeader()
    },

    mounted() {
    
    },

    computed: {
        ...mapGetters({ user: "user" }),

        manyUserText(){
            return `Group with ${Object.keys(this.group.participants).length} participants`
        }
    },

    watch: {
        // these 2 watchers make sure header content is reset when participants or group name are changed
        'group.participants': function (newVal, oldVal){
            this.whatToShowInHeader()
        },

        'group.name': function (newVal, oldVal){
            this.whatToShowInHeader()
        },
        // -----------------------------------------------------------------------------------------------
    },

    methods:{
        /**
         * Determine what content should be displayed in Chat Window Header
         * 
         * List of users or group name.
         */
        whatToShowInHeader(){
            if(this.group.name){
                this.showComponent = 'group-name'
                return
            } 

            if(this.group.model_type == "PRIVATE"){
                this.showComponent = 'list-chat-participants'
                return
            }

            const numParticipants = Object.keys(this.group.participants).length

            if(numParticipants == 1){
                this.showComponent = 'default-show'
                return
            }

            if(numParticipants > 1 && numParticipants <= 3){
                this.showComponent = 'list-chat-participants'
                return
            }

            if(numParticipants >= 4){
                this.showComponent = 'many-user-text'
                return
            }

            this.showComponent = 'list-chat-participants'
        }
    },
}
</script>

<style>
.header-text {
    @apply text-base text-white font-semibold ml-2;
}
</style>