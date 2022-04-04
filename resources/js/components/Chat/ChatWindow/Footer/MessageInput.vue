<template>
    <div class="w-full flex ">
        
        <!-- Message input -->
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
        'groupId',
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
            this.sendMessage();
        },

        sendMessage()
        {
            if(this.message===''){
                return;
            }

            axios.post(this.storeMessageEndpoint, this.formatMessage())
            .then(res => {
                if( res.status === 201 ){
                    this.message = '';
                    this.$emit('messageSent');
                }
            })
            .catch( error => {
                console.log(error);
            })
        },

        userTyping()
        {
            Echo.private("group." + this.groupId)
            .whisper('typing', {
                'id': this.user.id,
                'first_name': this.user.first_name,
                'last_name': this.user.last_name,
            });
        },

        formatMessage()
        {
            return {
                text: this.message,
                chat_group_id: this.groupId,
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