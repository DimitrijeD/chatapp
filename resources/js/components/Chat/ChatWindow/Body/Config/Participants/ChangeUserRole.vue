<template>
    <div>
        <div v-if="pendingRoleChangeAccept">
            <div class="text-center grid grid-cols-4 gap-2 bg-yellow-100 p-1">
                <span class="col-span-2">Make user {{ this.chosenNewRole.toLowerCase() }}</span>

                <button
                    class="w-full text-white bg-green-500 hover:bg-green-600"
                    @click="confirmedChangeRole"
                >Yes</button>

                <button
                    class="w-full text-white bg-red-500 hover:bg-red-600"
                    @click="declinedChangeRole"
                >No</button>
            </div>
        </div>
        <div v-else>
            <select 
                @change="roleUpdate($event)" 
                class="text-base font-light text-gray-700 w-full px-3 py-1.5 participant-role-color focus:outline-none focus:ring-2 focus:border-primary ring-inset"
            >
                <option class="block w-full" selected>{{ participant.pivot.participant_role.toLowerCase() }}</option>
                <option 
                    v-for="toRole in changeableRoles"
                    :value="toRole"
                    class="block w-full"
                > {{ toRole.toLowerCase() }} </option>
            </select>
        </div>
    </div>
</template>

<script>

export default{

    props:[
        'participant_id', 'changeableRoles', 'group_id'
    ],

    data(){
        return {
            pendingRoleChangeAccept: false,
            chosenNewRole: null
        }
    },

    computed: {
        participant(){
            return this.$store.getters['groups/getParticipant']({
                group_id: this.group_id,
                participant_id: this.participant_id
            })
        }
    },

    created(){
        
    },

    methods: {
        roleUpdate(e)
        {
            this.chosenNewRole = e.target.value
            this.pendingRoleChangeAccept = true
        },

        confirmedChangeRole()
        {
            this.$store.dispatch('groups/changeParticipantRole', {
                target_user_id: this.participant.id,
                group_id: this.group_id,
                to_role: this.chosenNewRole,
            })

            this.resetVars()
        },

        declinedChangeRole()
        {
            this.resetVars()
        },

        resetVars()
        {
            this.pendingRoleChangeAccept = false
            this.chosenNewRole = null
        }
    },

}

</script>