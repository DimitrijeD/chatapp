<template>
    <div class="flex m-2 gap-2 ">
        <textarea
            class="rounded-lg flex-grow p-2 resize-none text-base focus:outline-none shadow-inner"
            rows="3"
            @keyup.enter="whenMessageSent()"
            @keydown="userTyping"
            type="text"
            v-model="message"
            placeholder="Message ..."
        ></textarea>

        <button
            @click="whenMessageSent()"
            class="text-base flex-none w-10 "
        >
            <div class="text-blue-400 hover:text-blue-500">send</div> 
        </button>
    </div>
</template>

<script>
import { mapGetters } from "vuex";
import * as ns from '../../../../store/module_namespaces.js'

export default {

    props:[
        'group_id',
    ],

    data(){
        return{
            message: '',
            gm_ns: ns.groupModule(this.group_id),
        }
    },

    computed: {
        ...mapGetters({ user: "user" }),

    },

    methods: {
        whenMessageSent(){
            this.sendMessage()
            this.userStopedTyping()
        },

        sendMessage(){
            if(this.message === '') return

            this.$store.dispatch(this.gm_ns + '/storeMessage', this.getMessageFormat()).then(()=> {
                this.message = ''
            }).catch(error => {
                console.log('message input component')
                console.log(error)
            })
        },

        userTyping(){
            Echo.private("group." + this.group_id).whisper("typing", this.getWhisperData())
        },

        userStopedTyping(){
            Echo.private("group." + this.group_id).whisper("stoped-typing", this.getWhisperData())
        },

        getMessageFormat(){
            return {
                text: this.message,
                group_id: this.group_id,
                user_id: this.user.id
            };
        },

        getWhisperData(){
            return {
                'id': this.user.id,
                'first_name': this.user.first_name,
                'last_name': this.user.last_name,
            }
        },
    }
}
</script>

<style scoped>

</style>