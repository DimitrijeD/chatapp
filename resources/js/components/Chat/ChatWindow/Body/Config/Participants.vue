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
                <div class="mr-2"> <!-- Create small offset to right to prevent scroll and remove user icon overlap -->
                    <div 
                        class="grid grid-cols-12 my-2 mx-1 items-center"
                        v-for="participant in participants"
                        :key="participant.id"
                    >
                        <div class="col-span-6 cursor-pointer">
                            <small-user 
                                :user="participant" 
                            /> 
                        </div>

                        <div class="col-span-5">
                            <div v-if="canPromoteDemote(participant)">
                                <change-user-role
                                    :participant_id="participant.id"
                                    :changeableRoles="permissions[change_role][getPrticipantRole(participant)]"
                                    :group_id="group.id"
                                />
                            </div>
                            <div v-else class="text-gray-700 participant-role-color text-base font-light w-full px-4 py-1.5">
                                {{ getParticipantRoleForHumans(participant) }}
                            </div>
                        </div>

                        <font-awesome-icon 
                            icon="xmark" 
                            v-if="canRemove(participant)"
                            @click="removeParticipant(participant.id)"
                            class="text-center bg-red-400 w-6 h-6 rounded-full text-gray-50 hover:text-white hover:bg-red-500 ml-auto cursor-pointer"
                        />  
                    </div>
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
        'group', 'chatRole', 'permissions', 'roles'
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

    computed: {
        participants(){
            return this.sortParticipantsByRoleHierarchy(this.$store.getters['groups/getMyParticipants'](this.group.id))
        },
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

        canRemove(participant)
        {
            if(!this.actionRule_ParticipantNotSelf(participant.id)) return false

            if(!this.permissions.remove.includes(this.getPrticipantRole(participant))) return false 
            
            return true
        },

        canPromoteDemote(participant)
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
        },

        sortParticipantsByRoleHierarchy(participants)
        {
            let groupedByRole = {}
            let sortedByRole = []

            for(let i in this.roles){
                groupedByRole[this.roles[i]] = []
            }

            for(let i in participants){
                groupedByRole[participants[i].pivot.participant_role].push(participants[i]) 
            }

            for(let i in groupedByRole){
                sortedByRole = sortedByRole.concat(groupedByRole[i]);
            }

            return sortedByRole
        }

    }
}
</script>

<style>
.participants-h{
    height: 450px;
}

.participant-role-color {
    background-color: #eaedf9;
}
</style>