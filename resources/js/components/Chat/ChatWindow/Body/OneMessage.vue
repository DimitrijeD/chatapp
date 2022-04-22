<template>
    <div class="my-2.5 border-2 border-opacity-20"
        :class="{
        'bg-blue-100 rounded-r-2xl rounded-bl-2xl border-blue-300': !isSelf,
        'bg-gray-100 rounded-l-2xl rounded-br-2xl border-gray-300': isSelf,
    }">
        <div class="p-3">
            <img
                :src="message.user.thumbnail"
                alt="no img :/"
                class="w-16 h-16 mr-3 mb-0.5 select-none object-cover border-2 border-gray-200 rounded-full shadow-sm float-left"
            >
            <div class="mb-3 text-base font-serif">
                <!-- Users Name -->
                <span class="text-gray-600 truncate font-medium">
                    {{ message.user.first_name }} {{ message.user.last_name }}
                </span>
                                
                <!-- When was message created -->
                <vue-moments-ago
                    class="text-gray-500 float-right pr-2.5"
                    prefix=""
                    suffix="ago"
                    :date="message.created_at"
                    lang="en"
                />
            </div>
            <p class="mt-0.5 break-all text-base text-gray-700 font-sans tracking-wide ">
                {{ message.text }}
            </p>
        </div>
    </div>
</template>

<script>

import VueMomentsAgo from 'vue-moments-ago';
import { mapGetters } from "vuex";

export default {
    props: [
        'message',
    ],

    data(){
        return{
            isSelf: false,
        }
    },

    components:{
        'vue-moments-ago': VueMomentsAgo,
    },

    created(){

    },

    computed: {
        ...mapGetters({ user: "StateUser" }),

    },

    mounted() {
        this.whichUserIsIt();
    },

    methods:{
        whichUserIsIt()
        {
            if(this.message.user.id == this.user.id){
                this.isSelf = true;
            } else {
                this.isSelf = false;
            }
        },
    },

}
</script>
