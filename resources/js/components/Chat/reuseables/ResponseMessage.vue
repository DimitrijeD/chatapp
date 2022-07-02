<template>
    <div v-if="show" :class="[div1]" >
        <div :class="[div2]">
            <div :class="[div3]" class="w-full">

                <div class="relative">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        :class="[close]"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                <div :class="[msgsLayout]">
                    <p :class="[msg]" class="cursor-pointer">
                        Message 1
                    </p>
                </div> 
            </div>
        </div>
    </div>
</template>

<script>
// one component will contain all messages
export default{
    props: [
        'message', 'classes', 'expiresAfterMs' 
    ],

    data(){
        return {
            defaultClasses: {
                div1: "mx-4",
                div2: "",
                div3: "bg-gray-400 ",
                close: "text-white m-1 hover:text-white absolute top-0 right-0 h-6 w-6",
                msgsLayout: "text-center p-4",
                msg: "bg-gray-600 text-white",
                
            },
            show: true,

        }
    },

    computed:{
        div1(){
            return this.classes?.div1 ? this.classes.div1 : this.defaultClasses.div1 

        },

        div2(){
            return this.classes?.div2 ? this.classes.div2 : this.defaultClasses.div2
        },

        div3(){
            return this.classes?.div3 ? this.classes.div3 : this.defaultClasses.div3
        },

        close(){
            return this.classes?.close ? this.classes.close : this.defaultClasses.close
        },

        msgsLayout(){
            return this.classes?.msgsLayout ? this.classes.msgsLayout : this.defaultClasses.msgsLayout
        },

        msg(){
            return this.classes?.msg ? this.classes.msg : this.defaultClasses.msg
        },
        
    },

    created(){

    },

    mounted(){

    },

    methods:{

    },
}

</script>

<style>

</style>