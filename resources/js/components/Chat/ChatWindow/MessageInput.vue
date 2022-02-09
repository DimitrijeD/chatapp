<template>
    <div class="w-full flex ">

        <!-- Message input area -->
        <textarea
            class="flex-auto bg-blue-50 px-2 w-full resize-none text-sm focus:bg-white focus:outline-none focus:ring-1 focus:border-primary ring-inset"
            rows="3"
            @keyup.enter="whenMessageSent()"
            @keydown="userTyping"
            type="text"
            v-model="message"
            placeholder="type..."
        ></textarea>

        <button
            @click="whenMessageSent()"
            class="flex-auto p-2 pl-3 pr-3 bg-blue-500 text-white"
        >
            Send
        </button>

    </div>
</template>

<script>
export default {
    data(){
        return{
            message: '',
        }
    },

    props:[
        'groupId',
        'userSelf'
    ],

    created(){

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

            let url = '/api/chat/group/' + this.groupId + '/message';
            axios.post(url, {
                text: this.message
            })
            .then(res => {
                // console.log(res.data);
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
                'id': this.userSelf.id,
                'firstName': this.userSelf.firstName,
                'lastName': this.userSelf.lastName,
            });
        },
    }
}
</script>

