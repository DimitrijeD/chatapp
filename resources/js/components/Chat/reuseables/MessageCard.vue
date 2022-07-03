<template>
    <div v-if="message">
        <div class="m-1" style="display: flex; flex-direction: column; height: 100%;">
            <div class="">
                <small-user 
                    :user="message.user" 
                    :showOnly="'first_name'"
                    :imgCls="'w-8 h-8'" 
                    :userNameCls="'text-coolGray-700'"
                />
            </div>

            <div class="mx-2 text-sm font-light text-gray-600 font-sans multi-line-ellipsis" style="flex: 1; position: relative;">
                <p style="margin: 0; top: 50%; -ms-transform: translateY(-50%); transform: translateY(-50%); margin: auto;">
                    {{ message.text }}
                </p>
            </div>

            <div class="flex flex-row-reverse">
                <vue-moments-ago
                    class="text-gray-400 m-1 "
                    prefix=""
                    suffix="ago"
                    :date="message.created_at"
                    lang="en"
                />
            </div>
        </div>
    </div>
</template>

<script>
import VueMomentsAgo from 'vue-moments-ago'
import SmallUser from "./SmallUser.vue"

export default {
    props: [
        'message', 
    ],

    components: {
        'vue-moments-ago': VueMomentsAgo,
        'small-user': SmallUser,
    },

    computed: {
        
    },

    data(){
        return {

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
  text-overflow:ellipsis;
  overflow:hidden;
  display: -webkit-box !important;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  white-space: normal;
}
</style>