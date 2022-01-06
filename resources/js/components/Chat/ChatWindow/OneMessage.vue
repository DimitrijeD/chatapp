<template>
    <div class="mb-1.5 mr-1.5">
        <!--            v-bind:class="{
                'hover:bg-blue-50': !isSelf,
                'border-blue-300':  !isSelf,
                'border-gray-300':   isSelf,
            }"-->
        <div class="mt-2 ml-1">
            <!-- Wrapper for user name, image and message so that 'message sent time ago' is below -->
            <div class="flex justify-start ">

                <!-- Users small thumbnail and first name -->
                <div class="w-24">
                    <img
                        :src="message.user.thumbnail"
                        alt="no img :/"
                        class="bg-cover bg-center"
                    >

                    <div class="text-sm mt-1 text-gray-500 truncate">
                        {{ message.user.firstName }}
                    </div>
                </div>

                <!-- Message text box -->
                <div class="ml-1.5 mb-0.5 w-full">
                    <div class="p-3 break-all text-sm text-gray-800 rounded-tr-lg rounded-bl-lg"
                        v-bind:class="{
                            'bg-blue-200': !isSelf,
                            'bg-gray-200':  isSelf,
                        }"
                    >
                        {{ message.text }}
                    </div>

                    <!-- When was message created -->
                    <vue-moments-ago
                        class="text-gray-400 float-right mt-0.5"
                        prefix=""
                        suffix="ago"
                        :date="message.created_at"
                        lang="en"
                    />
                </div>

            </div>

        </div>
    </div>
</template>

<script>

import VueMomentsAgo from 'vue-moments-ago';

export default {
    props: [
        'message',
        'userSelf',
    ],

    data(){
        return{
            isSelf: false,
        }
    },

    components:{
        'vue-moments-ago': VueMomentsAgo,
    },

    mounted() {
        this.whichUserIsIt();
    },

    methods:{
        whichUserIsIt()
        {
            if(this.message.user.id == this.userSelf.id){
                this.isSelf = true;
            } else {
                this.isSelf = false;
            }
        },
    },

}
</script>

<style>
.border-l-3{
    border-left-width: 4px;
}
</style>
