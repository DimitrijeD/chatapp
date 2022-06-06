<template>
    <div class="w-full m-auto z-50 truncate">
        <!-- Make overflow when there are many users in this chat -->
        <!-- make status dynamic -->

        <div class="flex justify-start ml-2" >
            <!-- Do not show self in chat window heading because this.participants contains all users that belong to this chat-->
            <div
                v-for="participant in group.participants"
                :key="participant.id"
            >            
                <div v-if="participant.id != user.id" 
                    class="flex justify-start mr-2"
                >
                    <!-- Next div is just green circle as 'user is online' indicator; replace after this is done dynamically -->
                    <div class="m-auto">
                        <div class="w-3 h-3 rounded-full bg-green-400" />
                    </div>

                    <div class="text-base text-white font-semibold ml-2 hover:border-white border-b-2 border-blue-500 cursor-pointer nowrap">
                        {{ participant.first_name }} {{ participant.last_name }}
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import { mapGetters } from "vuex";

export default {
    props:[
        'group',
    ],

    data(){
        return{

        }
    },

    created() {
    
    },

    mounted() {
    
    },

    computed: {
        ...mapGetters({ user: "StateUser" }),
    },

    methods:{
        
    },
}
</script>

<style scoped>
.nowrap {
    white-space: nowrap ;
}
</style>