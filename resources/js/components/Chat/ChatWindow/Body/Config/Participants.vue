<template>
    <div class="">
        <div class="mb-2 border-b-2 border-gray-200">
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
                    class="grid grid-cols-12 my-2 mx-1 items-center"
                    v-for="participant in group.participants"
                    :key="participant.id"
                >
                    <div class="col-span-6 cursor-pointer">
                        <small-user :user="participant" /> 
                    </div>

                    <div class="col-span-5">
                        <change-user-role
                            v-if="isActionPermissible(change_role, participant)"
                            :participant_id="participant.id"
                            :changeableRoles="permissions[change_role][getPrticipantRole(participant)]"
                            :role="role"
                            :group_id="group.id"
                        />
                        <div 
                            v-else
                            class="text-gray-700 text-base font-light flex w-full px-4 py-1.5"
                        >
                            {{ getParticipantRoleForHumans(participant) }}
                        </div>
                    </div>
                    

                    <font-awesome-icon 
                        icon="xmark" 
                        @click="removeParticipant(participant.id)"
                        v-if="isActionPermissible(remove, participant)"
                        class="text-center bg-red-400 w-6 h-6 rounded-full text-gray-50 hover:text-white hover:bg-red-500 ml-auto cursor-pointer"
                    />  
                </div>
            </vue-scroll>
        </div>
    </div>
</template>

<script>
import SmallUser from '../../../reuseables/SmallUser.vue'
import ChangeUserRole from './Participants/ChangeUserRole.vue'

export default {
    props: [
        'group', 'role', 'permissions'
    ],

    components: {
        'small-user': SmallUser,
        'change-user-role': ChangeUserRole,
    },

    data() {
        return {
            user: this.$store.state.auth.user,
            change_role: 'change_role',
            remove: 'remove',
        }
    },

    created()
    {
       
    },

    methods: 
    {
        removeParticipant(id)
        {
            this.$store.dispatch('groups/removeParticipantFromGroup', {
                group_id: this.group.id,
                user_id_to_remove: id    
            }).then(()=>{
                // show success message that user was successfully added to group
            })
        },

        isActionPermissible(actionType, participant)
        {
            switch(actionType){
                case this.change_role:
                    return this.action_PromoteAndDemoteRole(participant)

                case this.remove:
                    return this.action_RemoveUser(participant)

                default:
                    return false

            }

        },

        action_RemoveUser(participant)
        {
            if(!this.actionRule_ParticipantNotSelf(participant.id)) return false

            if(!this.permissions.remove.includes(this.getPrticipantRole(participant))) return false 
            
            return true
        },

        action_PromoteAndDemoteRole(participant)
        {
            if(!this.actionRule_ParticipantNotSelf(participant.id, this.user.id)) return false

            let fromRoles = Object.keys(this.permissions.change_role)
            
            if(!this.actionRule_RuleTableNotEmpty(fromRoles)) return false

            if(!fromRoles.includes(this.getPrticipantRole(participant))) return false // participant is not among roles which can be changed under these conditions

            return true
        }, 

        actionRule_ParticipantNotSelf(participant_id)
        {
            return participant_id == this.user.id ? false : true
        },

        actionRule_RuleTableNotEmpty(permissionKeys)
        {
            return permissionKeys.length == 0 ? false : true
        },

        getPrticipantRole(participant)
        {
            return participant.pivot.participant_role
        },

        getParticipantRoleForHumans(participant)
        {
            return participant.pivot.participant_role.toLowerCase()
        }

    }
}
</script>

<style scoped>
.participants-h{
    height: 450px;
}
</style>