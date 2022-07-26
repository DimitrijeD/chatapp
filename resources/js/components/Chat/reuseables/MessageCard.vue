<template>
    <div class="page mx-1" :class="[heightCls]">
        <small-user 
            v-if="messageExists"
            class="header"
            :user="message.user" 
            :showOnly="'first_name'"
            :imgCls="'w-8 h-8'" 
            :userNameCls="'text-coolGray-700'"
        />

        <div class="content mx-1 my-1.5">
            <div v-if="messageExists" class="m-1 multi-line-ellipsis font-normal text-gray-800 font-sans">
                {{ message.text }}
            </div>
            <div v-else class="m-1 multi-line-ellipsis font-normal text-gray-800 font-sans">
                {{ noMessageText }}
            </div>
        </div>

        <div class="footer">
            <vue-moments-ago
                v-if="messageExists"
                class="font-light text-gray-600 text-right m-1 block truncate"
                prefix=""
                suffix="ago"
                :date="message.created_at"
                lang="en"
            />
        </div>
    </div>
</template>

<script>
import VueMomentsAgo from 'vue-moments-ago'
import SmallUser from "./SmallUser.vue"

export default {
    props: [
        'message', 'heightCls'
    ],

    components: {
        'vue-moments-ago': VueMomentsAgo,
        'small-user': SmallUser,
    },

    computed: {
        messageExists(){
            return this.message.id ? true : false
        }
    },

    data(){
        return {
            noMessageText: "There are no messages in this group."
        }
    },

    created(){
        
    },

    methods: {

    },

}
</script>

<style>
.multi-line-ellipsis{
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}


.page {
    height: 100%;
    display: flex;
    flex-direction: column;

}
.header {
    flex-shrink: 0;
}
.content {
    flex-grow: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    box-shadow: 0px 0px 1px 1px rgb(0 0 0 / 15%);
}
.footer {
    width: 100%;
    flex-shrink: 0;
}

</style>