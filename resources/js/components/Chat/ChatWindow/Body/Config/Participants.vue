<template>
    <div class="">
        <div class="mb-2 border-b-2 border-gray-200" v-if="hasRoles(['CREATOR'])">
            <div>
                <input 
                class="w-full p-3 text-base focus:bg-white focus:outline-none focus:ring-2 focus:border-primary ring-inset"
                type="text" 
                placeholder="todo input for filtering these users"
                > 
            </div>
        </div>

        <div class="participants-h">
            <vue-scroll>
                <div 
                    class="grid grid-cols-12 m-2 items-center"
                    v-for="participant in group.participants"
                    :key="participant.id"
                >
                    <div class="col-span-5 cursor-pointer">
                        <small-user :user="participant" /> 
                    </div>

                    <span class="col-span-2 text-sm select-none "> 
                        {{ participant.pivot.participant_role.toLowerCase() }}
                    </span>

                    <button 
                        class="col-span-4 p-1.5 text-gray-50 bg-green-400 hover:text-white hover:bg-green-500 font-sm rounded-lg cursor-pointer"
                        v-if="hasRole('CREATOR') && participant.id != user.id"
                    >Set moderator</button>

                    <font-awesome-icon 
                        icon="xmark" 
                        @click="removeParticipant(participant.id)"
                        v-if="hasRole('CREATOR') && participant.id != user.id"
                        class="text-center bg-red-400 w-6 h-6 rounded-full text-gray-50 hover:text-white hover:bg-red-500 ml-auto cursor-pointer"
                    />  
                </div>
            </vue-scroll>
        </div>
    </div>
</template>

<script>
import SmallUser from '../../../reuseables/SmallUser.vue'

export default {
    props: [
        'group', 'role'
    ],

    components: {
        'small-user': SmallUser,
    },

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
            this.$store.dispatch('groups/removeParticipantFromGroup', {
                group_id: this.group.id,
                user_id_to_remove: id    
            }).then(()=>{
                // show success message that user was successfully added to group
            })
        }
    }
}
</script>

<style scoped>
.participants-h{
    height: 450px;
}
</style>