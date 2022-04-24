<template>
    <div>
        <button
            @click="manageDropdown()"
            class="z-40 width-300 height-44 py-2 pr-2 text-xl font-medium"
            :class="{
                'hover:bg-blue-800': !showExistingChats,
                // Doesn't have unseen messages and dropdown is open
                'text-blue-50 bg-blue-800 hover:bg-blue-600': !newMessage && showExistingChats,

                // Doesn't have unseen messages and dropdown is closed
                'text-gray-50': !newMessage && !showExistingChats,

                // Has unseen messages and dropdown is open
                'bg-green-500 font-semibold text-white': newMessage && showExistingChats,

                // Has unseen messages and dropdown is closed
                'font-semibold text-green-200 hover:text-green-300': newMessage && !showExistingChats,
            }"
        >
            {{ dropdownText }}
        </button>

        <transition name="slide-fade-nav-dropdowns">
            <div
                v-if="showExistingChats"
                class="z-30 width-300 absolute bg-gray-100 overflow-y-auto h-96 mt-1 shadow-2xl "
            >
                <div class="m-2">
                    <input
                        class="w-full p-2 bg-white text-base hover:bg-blue-50 focus:bg-blue-100 focus:outline-none focus:ring-2 focus:border-primary ring-inset"
                        placeholder="Find chats by user"
                        type="text"
                        v-model="searchStr"
                        @keyup="searchInput"
                    >
                </div>

                <div class="flex flex-col-reverse">
                    <div v-for="group in groups" :key="group.id">
                        <div
                            class="p-2 m-2 rounded-xl cursor-pointer shadow-inner"
                            @click="createChatWindow(group)"
                            :class="{
                                'bg-blue-100 hover:bg-blue-200': !group.hasUnseenState,
                                'bg-green-200 hover:bg-green-300': group.hasUnseenState,
                            }"
                        >
                            <!-- Group Name -->
                            <div class="mb-1">
                                <p v-if="group.name" class="p-0.5 m-0.5 text-gray-700 overflow-ellipsis break-words ">
                                    {{group.name}}
                                </p>

                                <p v-else class="p-0.5 m-0.5 text-gray-700">
                                    No name
                                </p>
                            </div>

                            <!-- List of all participants in 'this' chat group -->
                            <!-- @todo Need overflow when there are too many participants in list -->
                            <span
                                v-for="participant in group.participants"
                                :key="participant.id"
                            >
                                <span 
                                    v-if="participant.id !== user.id"
                                    class="bg-blue-400 text-white rounded p-1.5 m-1"
                                >
                                    {{ participant.first_name }} {{ participant.last_name }}
                                </span>
                            </span>

                        </div>
                    </div>
                </div>

                <div v-if="nothingFound" class="m-2 text-red-500">
                    {{ nothingFound }}
                </div>
            </div>
         </transition>
    </div>
</template>

<script>
import { mapGetters } from "vuex";

export default {
    data(){
        return {
            showExistingChats: false,
            searchStr: '',
            nothingFound: '',

            newMessage: false,
        }
    },

    computed: {
        ...mapGetters({ 
            user: "StateUser",
            groups: "groups/filteredGroups",
        }),

        dropdownText(){
            let num = this.$store.getters['groups/numOfUnseenGroups']
            if(!num) {
                this.newMessage = false
                return 'Chat groups'
            }
            this.newMessage = true
            return 'New Messages in ' + num + ' chats'
        }
    },

    created(){  
        
    },

    mounted() {
        this.groupsWithUnseenMessages
    },

    methods:{
        manageDropdown()
        {
            this.showExistingChats = !this.showExistingChats;
            // Intent to open window
            if(this.showExistingChats){
                // refresh current array of groups - Intent to open window
                // this.getGroupsByUserWithParticipants();
            }
        },

        createChatWindow(group)
        {
            this.$store.dispatch('groups/openGroup', group.id)
            this.showExistingChats = false;
        },

        searchInput()
        {
            this.nothingFound = '';
            // @todo req exp bugs when there are certain characters in string such as '?, *'

            //this.$store.getters.filterGroupsBySearchString(this.searchStr)
            this.$store.dispatch('groups/filterGroupsBySearchString', this.searchStr)
            
            if(!this.groups.length){
                this.nothingFound = 'Nothing found :/';
            }
        },
    }
}
</script>

<style>
.width-300{
    width: 300px;
}

.height-44{
    height: 44px;
}

.height-480{
    height: 480px;
}
</style>
