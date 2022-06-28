<template>
    <span class="text-base text-white font-semibold ml-2 hover:border-white border-b-2 border-blue-500 cursor-pointer nowrap">{{ group.name }}</span>
</template>

<script>
import { mapGetters } from "vuex";

export default {
    props: [
        'group',
    ],

    data(){
        return {

        }
    },

    computed: {
        ...mapGetters({ user: "StateUser" }),
    },

    methods:{

    },
}
</script>