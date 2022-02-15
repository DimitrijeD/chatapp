<template>
    <div class="flex space-x-2 m-3">

        <!-- Users thumbnail -->
        <div class="flex-none w-20 h-20">
            <img
                :src="message.user.thumbnail"
                alt="no img :/"
                class="object-cover relative border border-gray-100 shadow-sm h-full"
            >
        </div>


        <div class="flex-grow">
            <!-- Users First Name -->
            <div >
                <span class="text-sm mb-1.5 text-gray-700 truncate">
                    {{ message.user.firstName }} {{ message.user.lastName }}
                </span>
                                
                <!-- When was message created -->
                <vue-moments-ago
                    class="text-gray-400 text-sm float-right mb-1.5"
                    prefix=""
                    suffix="ago"
                    :date="message.created_at"
                    lang="en"
                />
            </div>

            <!-- Message text box -->
            <div class="w-full">
                <div class="p-3 break-all text-base text-gray-700 rounded-tr-lg rounded-bl-lg filter drop-shadow-md"
                    v-bind:class="{
                        'bg-blue-100': !isSelf,
                        'bg-gray-100':  isSelf,
                    }"
                >
                    {{ message.text }}
                </div>


            </div>
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