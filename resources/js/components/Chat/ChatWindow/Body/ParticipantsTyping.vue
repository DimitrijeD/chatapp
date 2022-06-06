<template>
    <div class="m-2">
        <div
            v-for="user in usersTyping"
            v-bind:key="user.id"
            class="italic text-sm text-gray-500 ml-2 select-none"
        >
            <div v-if="user">
                {{ user.first_name }} is typing ...
            </div>
        </div>
    </div>
</template>

<script>

export default {
    props:[
        'group_id',
    ],

    /**
     * usersTyping = {
     *     int user1_Id: user1,
     *     int user2_Id: user2,
     *     ...
     * }
     *
     * Each time user1 types, his value and timer is reset.
     * When user stops typing for 'config.removeTyperAfterMS' milliseconds, his name will dissapear from DOM.
     * Feature is supposed to work for any number of chat participants utilizing hash table 'usersTyping'.
     *
     */
    data(){
        return{
            usersTyping: {},
            usersTimeouts: {},
            config: {
                removeTyperAfterMS: 3000,
            },
        }
    },

    mounted() {
        Echo.private("group." + this.group_id)
        .listenForWhisper('typing', user => {
            this.addOrUpdateTyper(user)
        })

        Echo.private("group." + this.group_id)
        .listenForWhisper('stoped-typing', user => {
            this.removeTyper(user.id)
        })
    },

    methods:{
        addOrUpdateTyper(user)
        {
            this.$emit('typing')

            let temp = {}
            temp[user.id] = user

            this.usersTyping = Object.assign({}, this.usersTyping, temp)
            this.addOrResetTimer(user.id)
        },

        removeTyper(id)
        {
            this.$delete(this.usersTyping, id)
        },

        addOrResetTimer(id)
        {
            if(this.usersTimeouts[id]){
                clearTimeout(this.usersTimeouts[id])
            }
            this.usersTimeouts[id] = setTimeout(() => {
                this.removeTyper(id)
            }, this.config.removeTyperAfterMS)
        },

    },

}
</script>
