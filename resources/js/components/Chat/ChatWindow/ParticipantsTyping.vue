<template>
    <div>
        <div
            v-if="userTyping"
            class="italic text-sm text-gray-500 ml-2"
        >
            {{ userTyping.firstName }} is typing ...
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
            typingTimer: null,
        }
    },

    mounted() {
        Echo.private("group." + this.groupId)
        .listenForWhisper('typing', e => {
            this.userTyping = e;
            if(this.typingTimer){
                clearTimeout(this.typingTimer);
            }

            this.typingTimer = setTimeout(() => {
                this.userTyping = null;
            }, 3000);
        });
    },

}
</script>
