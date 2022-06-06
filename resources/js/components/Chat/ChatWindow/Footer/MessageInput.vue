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
            <font-awesome-icon 
                icon="message" 
                size="2xl" 
                class="text-blue-400 hover:text-blue-500"
            /> 
        </button>
    </div>
</template>

<script>
import { mapGetters } from "vuex";

export default {
    data(){
        return{
            message: '',
            config: {
                channel: {
                    name: "group." + this.group_id,
                    events: {
                        typing: {
                            name: 'typing'
                        },

                        stopTyping: {
                            name: 'stoped-typing'
                        },
                    }
                },
            },
        }
    },

    props:[
        'group_id',
    ],

    computed: {
        ...mapGetters({ user: "StateUser" }),

    },

    methods: {
        whenMessageSent(){
            this.sendMessage()
            this.userStopedTyping()
        },

        sendMessage()
        {
            if(this.message === '') return

            this.$store.dispatch('groups/storeMessage', this.getMessageFormat()).then(()=> {
                this.message = ''
            }).catch(error => {
                console.log('message input component')
                console.log(error)
            })
        },

        userTyping()
        {
            Echo.private(this.config.channel.name)
            .whisper(this.config.channel.events.typing.name, this.getWhisperData());
        },

        userStopedTyping()
        {
            Echo.private(this.config.channel.name)
            .whisper(this.config.channel.events.stopTyping.name, this.getWhisperData())
        },

        getMessageFormat()
        {
            return {
                text: this.message,
                group_id: this.group_id,
                user_id: this.user.id
            };
        },

        getWhisperData()
        {
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