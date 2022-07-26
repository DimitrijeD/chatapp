<template>
    <div v-if="user">
        <div class="bg-gray-50 shadow-xl py-3">


            <div class="p-2 text-center text-xl text-gray-900">
                <img class="mx-auto w-20 h-20" :src="user.image" alt="no img">

                <p class="">UserID: <b>{{ user.id }}</b> </p>
                <p class="">First and last name: <b>{{ user.first_name }} {{ user.last_name }}</b></p>

                <p class="p-2 text-gray-500 font-semibold">Email <b>{{ user.email }}</b></p>

                <!--
                <p>ICONS TESTING</p>
                     <font-awesome-icon 
                    icon="magnifying-glass" 
                    size="2x" 
                    class=""
                />
                <br>
                <font-awesome-icon 
                    icon="spinner" 
                    size="1x" 
                    class=""
                    spin fixed-width
                />   
                <br>
                <font-awesome-icon 
                    icon="envelope" 
                    size="2xs" 
                    class=""
                />   
                <font-awesome-icon 
                    icon="envelope" 
 
                    class=""
                />  
                <font-awesome-icon 
                    icon="envelope" 
                    size="2xl" 
                    class=""
                />   -->

            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from "vuex";

export default {
    data(){
        return{
            
        };
    },

    created()
    {
        
    },

    computed: {
        ...mapGetters({ user: "user" }),
    },

    mounted()
    {
        
    },


    methods: {
        
    }

}
</script>
