<template>
    <div class="mx-4 ">
        <div class="grid grid-cols-3 gap-2 m-2 mt-4">
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
       
        <div class="text-center ">
            <button
                v-if="awaitConfirmation" 
                class="w-full text-white bg-red-400 hover:bg-red-500 text-bold py-2 mt-10"
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
</template>

<script>
export default {
    props: [
        'group', 'role'
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
        }
    },

    created()
    {

    },

    methods: 
    {
        leaveGroup() { this.$store.dispatch('groups/leaveGroup', {group_id: this.group.id}) },

        askConfirmation() { this.awaitConfirmation = false },

        declined() { this.awaitConfirmation = true }

    }
}
</script>