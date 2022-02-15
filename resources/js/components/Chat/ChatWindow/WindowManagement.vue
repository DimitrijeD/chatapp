<template>
    <div class="inline-grid grid-cols-2 gap-x-2 m-auto pr-3">
        <!-- Minimize chat window -->
        <svg
            @click="minimize()"
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 text-blue-200 hover:text-white cursor-pointer"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 13l-7 7-7-7m14-8l-7 7-7-7" />
        </svg>

        <!-- Close chat window -->
        <svg
            @click="close()"
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 text-blue-200 hover:text-white cursor-pointer"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </div>
</template>

<script>
export default {
    props:[
        'windowIndex'
    ],

    data(){
        return {

        }
    },

    mounted() {

    },

    methods:{
        minimize()
        {
            this.$emit('minimizeWindow');
        },

        close()
        {
            this.$emit('closeWindow', this.windowIndex);
        },
    },

}
</script>
