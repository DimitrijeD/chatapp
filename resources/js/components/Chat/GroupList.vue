<template>
    <div>
        <button
            @click="manageDropdown()"
            class="z-40 text-center width-300 height-44 py-2 pr-2 text-xl font-medium"
            :class="{
                'hover:bg-blue-800': !show,
                // Doesn't have unseen messages and dropdown is open
                'text-blue-50 bg-blue-800 hover:bg-blue-600': !newMessage && show,

                // Doesn't have unseen messages and dropdown is closed
                'text-gray-50': !newMessage && !show,

                // Has unseen messages and dropdown is open
                'bg-green-500 font-semibold text-white': newMessage && show,

                // Has unseen messages and dropdown is closed
                'font-semibold text-green-200 hover:text-green-300': newMessage && !show,
        }">
            {{ dropdownText }}                 
            <font-awesome-icon 
                v-if="!newMessage"
                icon="envelope" 
                size="xl" 
                class="ml-1"
            /> 
            <font-awesome-icon 
                v-if="newMessage"
                icon="envelope-open" 
                size="xl" 
                class="ml-1"
            /> 
        </button>

        <transition name="slide-fade-nav-dropdowns">
            <div
                v-if="show"
                class="z-30 width-300 absolute bg-gray-100 shadow-2xl border-l-2 border-r-2 border-b-2 border-blue-200"
            >
                <div class="m-2">
                    <input
                        class="w-full p-2 bg-white text-base hover:bg-blue-50 focus:bg-blue-100 focus:outline-none focus:ring-2 focus:border-primary ring-inset"
                        placeholder="Find chats by user or chat name"
                        type="text"
                        v-model="searchStr"
                        @keyup="searchInput"
                    >
                </div>

                <div class="h-96">
                    <vue-scroll :ops="ops">
                        <div class="flex flex-col-reverse mx-1">
                            <div v-for="group in groups" :key="group.id">
                                <group-card
                                    @click.native="openChatWindow(group.id)"
                                    :group_id="group.id"
                                />
                            </div>
                        </div>
                    </vue-scroll>
                </div>


                <div v-if="nothingFound" class="m-2 text-red-500">
                    {{ nothingFound }}
                </div>
            </div>
         </transition>
    </div>
</template>

<script>
import { mapGetters } from "vuex"
import GroupCard from "./reuseables/GroupCard.vue"

export default {
    
    components: {
        'group-card': GroupCard
    },

    data(){
        return {
            show: false,
            searchStr: '',
            nothingFound: '',

            newMessage: false,

            ops: {
                scrollPanel: { scrollingX: false },
                bar: { 
                    keepShow: true,
                    background: '#73affa',
                }
            },
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
        }, 

    },

    created(){  
        
    },

    mounted() {
        
    },

    methods:{
        manageDropdown() { this.show = !this.show},

        openChatWindow(group_id)
        {
            this.$store.dispatch('groups/openGroup', group_id)
            this.show = false;
        },

        searchInput()
        {
            this.nothingFound = '';
            this.$store.dispatch('groups/filterGroupsBySearchString', this.searchStr)
            if(!this.groups.length) this.nothingFound = 'Nothing found :/';
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
