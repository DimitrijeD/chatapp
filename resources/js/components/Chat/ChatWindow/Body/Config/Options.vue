<template>
    <div class="">
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

        <button class="inset-x-0 bottom-0 w-full text-white bg-red-500 text-center text-bold py-2 mt-10">
            Leave group
        </button>
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
            selected: 'A',
            muteOptions: [
                { text: "Don't mute", value: '0' },
                { text: 'Minute', value: 'A' },
                { text: 'Hour', value: 'B' },
                { text: 'Day', value: 'C' },
                { text: 'Week', value: 'C' },
                { text: 'Month', value: 'C' },
                { text: 'Forever', value: 'C' },
            ]
        }
    },

    created()
    {

    },
}
</script>