<template>
    <div>
        <!-- @todo this only works properly for 1on1 chat. If multiple users type, values are just overridden -->
        <!-- @todo typing doesn't dissapear when message is received. It requires OneMessage component to emit event to reset someoneIsTyping['for that user] -->
        <div
            v-if="someoneIsTyping"
            class="italic text-sm text-gray-500 ml-2"
        >
            {{ userTyping.firstName }} {{ userTyping.lastName }} is typing...
        </div>

    </div>
</template>

<script>
export default {
    props:[
        'groupId',
    ],

    data(){
        return{
            userTyping: null,
            someoneIsTyping: false,
            typingTimer: null,
        }
    },

    mounted() {
        Echo.private("group." + this.groupId)
        .listenForWhisper('typing', e => {
            this.userTyping = e;
            this.someoneIsTyping = true;

            if(this.typingTimer){
                clearTimeout(this.typingTimer);
            }

            this.typingTimer = setTimeout(() => {
                this.someoneIsTyping = false;
                this.userTyping = null;
            }, 3000);
        });
    },



}
</script>
