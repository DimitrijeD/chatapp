<template>
    <div class="my-2.5 mx-2.5"
        :class="{
        'bg-blue-100 rounded-l-2xl rounded-tl-2xl a-shadow-blue': !isSelf,
        'bg-gray-200 rounded-r-2xl rounded-bl-2xl a-shadow-gray': isSelf,
    }">
        <div class="p-3">
            <img
                :src="message.user.thumbnail"
                alt="no img :/"
                class="float-left w-16 h-16 mr-3 mb-0.5 select-none object-cover rounded-full"
                :class="{
                    'shadow-msg-img-self': isSelf,
                    'shadow-msg-img-other': !isSelf
                }"
            >
            <div class="mb-3 text-base">
                <!-- Users Name -->
                <span class="text-gray-500 truncate text-lg font-semibold">
                    {{ message.user.first_name }} {{ message.user.last_name }}
                </span>
                                
                <!-- When was message created -->
                <vue-moments-ago
                    class="text-gray-500 float-right"
                    prefix=""
                    suffix="ago"
                    :date="message.created_at"
                    lang="en"
                />
            </div>
            <p class="mt-0.5 msg-txt text-gray-700 font-sans tracking-wide ">
                {{ message.text }}
            </p>
        </div>
    </div>
</template>

<script>

import VueMomentsAgo from 'vue-moments-ago'
import { mapGetters } from "vuex"

export default {
    props: [
        'message',
    ],

    data(){
        return{
            
        }
    },

    components:{
        'vue-moments-ago': VueMomentsAgo,
    },

    created(){

    },

    computed: {
        ...mapGetters({ user: "user" }),

        isSelf(){ return this.message.user.id == this.user.id ? true : false },
    },

    mounted() {
        
    },

    methods:{

    },

}
</script>

<style>

.a-shadow-gray {
	-webkit-box-shadow: -3px 8px 10px -6px rgb(45 35 35 / 42%);
	   -moz-box-shadow: -3px 8px 10px -6px rgb(45 35 35 / 42%);
	        box-shadow: -3px 8px 10px -6px rgb(45 35 35 / 42%);
}

.a-shadow-blue {
	-webkit-box-shadow: -5px 8px 10px -6px rgb(0 74 149 / 56%);
	   -moz-box-shadow: -5px 8px 10px -6px rgb(0 74 149 / 56%);
	        box-shadow: -5px 8px 10px -6px rgb(0 74 149 / 56%);
}

.shadow-msg-img-self {
    box-shadow: -1px 1px 3px 2px rgb(0 0 0 / 42%);
}

.shadow-msg-img-other {
    box-shadow: -1px 1px 3px 2px rgb(0 0 0 / 42%);
}

.msg-txt{
    font-size: 17px;
}
</style>