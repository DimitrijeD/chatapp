<template>
    <div class="relative z-40 mx-4">
        <div class="absolute inset-x-0 top-3">
            <div class="w-full bg-gray-400 ">

                <div class="relative">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="text-white m-1 cursor-pointer hover:text-white absolute top-0 right-0 h-6 w-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                <p class="text-center p-4">
                    Response messages
                </p> 
            </div>
        </div>
    </div>
</template>

<script>
// one component will contain all messages
export default{
    props: [
        'msg', 'classes', 'expiresAfterMs' 
    ],

    data(){
        return {
            defaultClasses: {
                layout: 1,

            }
        }
    },

    computed:{

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