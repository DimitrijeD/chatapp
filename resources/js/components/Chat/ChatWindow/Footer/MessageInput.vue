<template>
    <div class="w-full flex ">
        <textarea
            class="flex-auto bg-blue-50 p-2 w-full resize-none text-base focus:bg-white focus:outline-none focus:ring-1 focus:border-primary ring-inset"
            rows="3"
            @keyup.enter="whenMessageSent()"
            @keydown="userTyping"
            type="text"
            v-model="message"
            placeholder="type..."
        ></textarea>
        <button
            @click="whenMessageSent()"
            class="button-send-msg text-base bg-blue-400 text-white hover:bg-blue-500"
        >
            Send
        </button>
    </div>
</template>

<script>
import { mapGetters } from "vuex";

export default {
    data(){
        return{
            message: '',
            storeMessageEndpoint: '/api/chat/message/store'
        }
    },

    components:{
    },

    props:[
        'group_id',
    ],

    created() {

    },

    computed: {
        ...mapGetters({ user: "StateUser" }),

    },

    mounted() {

    },

    methods: {
        whenMessageSent(){
            this.sendMessage()
            this.userStopedTyping()
        },

        sendMessage()
        {
            if(this.message==='') return
            this.$store.dispatch('groups/storeMessage', this.formatMessage()).then(()=> {
                this.message = ''
            }).catch(error => {
                console.log('message input component')
                console.log(error)
            })
        },

        userTyping()
        {
            Echo.private("group." + this.group_id)
            .whisper('typing', {
                'id': this.user.id,
                'first_name': this.user.first_name,
                'last_name': this.user.last_name,
            });
        },

        userStopedTyping()
        {
            Echo.private("group." + this.group_id)
            .whisper('stoped-typing', {
                'id': this.user.id,
                'first_name': this.user.first_name,
                'last_name': this.user.last_name,
            })
        },

        formatMessage()
        {
            return {
                text: this.message,
                group_id: this.group_id,
                user_id: this.user.id
            };
        }
    }
}
</script>

<style scoped>
.button-send-msg{
  aspect-ratio: 1 / 1;
  height: 100%;
}
</style>