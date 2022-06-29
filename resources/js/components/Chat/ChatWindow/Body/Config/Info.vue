<template>
    <vue-scroll>
        <div class="grid grid-cols-3 gap-4 text-center">
            <div class=" m-1 p-2 flex items-center justify-center select-none">
                Group name
            </div>

            <div class="col-span-2 m-1 p-2 text-left select-all cursor-pointer">
                <span v-if="group.name">{{ group.name }}</span>
                <span v-else class="italic">This group has no name</span>
            </div>

            <div class=" m-1 p-2 flex items-center justify-center select-none">
                Number of participants
            </div>

            <div class="col-span-2 m-1 p-2 text-left select-all cursor-pointer">
                <span>{{ group.participants.length }}</span>
            </div>

            <div class=" m-1 p-2 flex items-center justify-center select-none">
                Group created
            </div>

            <div class="col-span-2 m-1 p-2 text-left select-all cursor-pointer">
                <span>{{ formatDate() }}</span>
            </div>

            <div class=" m-1 p-2 flex items-center justify-center select-none">
                Group type
            </div>

            <div class="col-span-2 m-1 p-2 text-left select-all cursor-pointer">
                <span>{{ group.model_type }}</span>
            </div>
        </div>
    </vue-scroll>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
    props: [
        'group', 'permissions'
    ],

    data() {
        return {
            
        }
    },

    computed: {
        ...mapGetters({ 
            user: "StateUser",
        }),
    },

    created()
    {
        
    },

    methods: 
    {
        formatDate()
        {
            return new Date(this.group.created_at)
        },

    }
}
</script>