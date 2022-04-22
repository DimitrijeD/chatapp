<template>
    <div class="">
        <div class="mb-10 border-b-2 border-gray-200" v-if="hasRoles(['CREATOR', 'MODERATOR'])">
            <div>
                <input 
                class="w-full p-3 text-base focus:bg-white focus:outline-none focus:ring-2 focus:border-primary ring-inset"
                type="text" 
                placeholder="input for filtering these users"
                > 
            </div>
        </div>

        <div 
            class="grid grid-cols-7 gap-2 m-3 cursor-pointer items-center"
            v-for="participant in group.participants"
            :key="participant.id"
        >
                <img
                    :src="participant.thumbnail"
                    alt="no img :/"
                    class="w-20 h-20 object-cover relative border border-gray-100 shadow-sm h-full"
                >

                <span class="col-span-2 text-sm hover:text-base">
                    {{ participant.first_name }} {{ participant.last_name }}
                </span>

                <span class="text-sm">
                    {{ participant.pivot.participant_role.toLowerCase() }}
                </span>

                <div
                    class="col-span-3 grid grid-cols-5 gap-1 items-center mx-auto" 
                    v-if="hasRole('CREATOR') && participant.id != user.id"
                >   
                    <div class="col-span-4">
                        <button class="p-1.5 text-gray-50 bg-green-400 hover:text-white hover:bg-green-500 font-sm rounded-lg">
                            Set moderator
                        </button>
                    </div>
                    <div class="">
                        <span 
                            @click="removeParticipant(participant.id)"
                            class="text-center bg-red-500 w-6 h-6 rounded-full text-gray-50 hover:text-white hover:bg-red-600 float-right"
                        >X
                        </span>
                    </div>
                </div>

                <div v-else>

                </div>

        </div>
    </div>
</template>

<script>
export default {
    props: [
        'group'
    ],

    data() {
        return {
            user: this.$store.state.auth.user,
        }
    },

    created()
    {
        
    },

    methods: {
        /**
         * if user has this exact role
         */
        hasRole(role)
        {
            for(let index in this.group.participants){
                let participantPivot = this.group.participants[index].pivot
                if(participantPivot.user_id == this.user.id && participantPivot.participant_role == role) return true
            }

            return false
        },

        /**
         * if user has any of roles 
         */
        hasRoles(roles)
        {
            for(let index in this.group.participants){
                let participantPivot = this.group.participants[index].pivot
                if(participantPivot.user_id == this.user.id){
                    for(let roleIndex in roles){
                        if(participantPivot.participant_role == roles[roleIndex]) return true
                    }
                    return false
                } 
            }
        },

        removeParticipant(id)
        {
            console.log(id)
        }
    }
}
</script>

<style scoped>

</style>