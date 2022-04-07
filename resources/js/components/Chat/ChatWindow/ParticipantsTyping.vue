<template>
    <div class="mb-2 mt-2">
        <div
            v-for="user in usersTyping"
            v-bind:key="user.id"
            class="italic text-sm text-gray-500 ml-2"
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
        'receivedMessage'
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
            this.addOrUpdateTyper(user);
        });
    },

    watch: {
        receivedMessage: {
            handler: function(newValue, oldValue) {
                this.removeTyper(newValue.user_id);
            }
        }
    },

    methods:{
        addOrUpdateTyper(user)
        {
            let temp = {};
            temp[user.id] = user;

            this.usersTyping = Object.assign({}, this.usersTyping, temp);
            this.addOrResetTimer(user.id);
        },

        removeTyper(id)
        {
            this.$delete(this.usersTyping, id);
        },

        addOrResetTimer(id)
        {
            if(this.usersTimeouts[id]){
                clearTimeout(this.usersTimeouts[id]);
            }
            this.usersTimeouts[id] = setTimeout(() => {
                this.removeTyper(id);
            }, this.config.removeTyperAfterMS);
        },

    },

}
</script>
