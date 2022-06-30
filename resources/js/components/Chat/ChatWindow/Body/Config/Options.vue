<template>
    <div class="mx-2 space-y-6">
        <div class="block" v-if="canChangeName">
            <div class="flex gap-2 mt-4 items-center">
                <div class="flex-none">
                    <span class="inline-block">Change group name</span>
                </div>

                <div class="flex-auto ">
                    <input 
                        type="text"
                        class="inline-block p-1 text-lg text-gray-600 w-full bg-gray-50 rounded border border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300" 
                        v-model="newGroupName"
                        :placeholder="group.name"
                    >
                </div>

                <div class="flex-auto ">
                    <button
                        class="inline-block p-2"
                        :class="{
                            'text-white bg-green-400 hover:bg-green-500 ': validateChangeName, 
                            'disabled text-gray-600 bg-gray-300': !validateChangeName,
                        }"
                        @click="changeGroupName"
                    >Save</button>
                </div>
            </div>
        </div>

        <div class="block">
            <div class="grid grid-cols-3 gap-2 m-2">
                <div class="p-1">
                    <span class="flex items-center justify-center">
                        Mute this group        
                    </span>
                </div>

                <div class="col-span-2 p-1">
                    <span class="flex items-center justify-center">
                        <select v-model="selected">
                            <option 
                                v-for="(option, index) in muteOptions" 
                                :value="option.value" 
                                :key="index"
                            >
                                {{ option.text }}
                            </option>
                        </select>
                        <span>
                            Selected: {{ selected }}
                        </span>
                    </span>
                </div>
            </div>
        </div>
       
        <div class="block">
            <div class="text-center">
                <button
                    v-if="awaitConfirmation" 
                    class="w-full text-white bg-red-400 hover:bg-red-500 text-bold py-2"
                    @click="askConfirmation"
                >Leave group</button>

                <div 
                    class="grid grid-cols-3 gap-2"
                    v-else
                >
                    <span>Are you sure you wish to leave this chat?</span>

                    <button
                        class="w-full text-white bg-red-500 hover:bg-red-600"
                        @click="leaveGroup"
                    >Yes</button>

                    <button
                        class="w-full text-white bg-green-500 hover:bg-green-600"
                        @click="declined"
                    >Close</button>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
export default {
    props: [
        'group', 'permissions'
    ],
    
    data() {
        return {
            user: this.$store.state.auth.user,
            selected: 'A',
            muteOptions: [
                { text: "Don't mute", value: '0' },
                { text: 'Minute', value: 'A' },
                { text: 'Hour', value: 'B' },
                { text: 'Day', value: 'C' },
                { text: 'Week', value: 'C' },
                { text: 'Month', value: 'C' },
                { text: 'Forever', value: 'C' },
            ],
            awaitConfirmation: true,
            newGroupName: null,
        }
    },

    computed: {
        validateChangeName(){
            if(this.newGroupName == this.group.name || (this.group.name === null && this.newGroupName === ''))
                return false

            return true
        },

        canChangeName(){
            return this.permissions.change_group_name ? true : false
        }
    },

    created()
    {
         
    },

    methods: 
    {
        leaveGroup() { this.$store.dispatch('groups/leaveGroup', {group_id: this.group.id}) },

        askConfirmation() { this.awaitConfirmation = false },

        declined() { this.awaitConfirmation = true },

        changeGroupName()
        {
            if(!this.validateChangeName) return

            this.$store.dispatch('groups/changeGroupName', {
                group_id: this.group.id,
                new_name: this.newGroupName
            })
        },

    }
}
</script>