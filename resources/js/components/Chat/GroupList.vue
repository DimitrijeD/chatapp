<template>
    <div>
        <button
            @click="manageDropdown()"
            class="z-40 text-center width-300 height-44 py-2 pr-2 text-xl font-medium"
            :class="{
                'hover:bg-blue-800': !show,
                // Doesn't have unseen messages and dropdown is open
                'text-blue-50 bg-blue-800 hover:bg-blue-600': !numGroupsWithUnseen && show,

                // Doesn't have unseen messages and dropdown is closed
                'text-gray-50': !numGroupsWithUnseen && !show,

                // Has unseen messages and dropdown is open
                'bg-green-500 font-semibold text-white': numGroupsWithUnseen && show,

                // Has unseen messages and dropdown is closed
                'font-semibold text-green-200 hover:text-green-300': numGroupsWithUnseen && !show,
        }">
            {{ dropdownText }}                 
            <font-awesome-icon 
                v-if="!numGroupsWithUnseen"
                icon="envelope" 
                size="xl" 
                class="ml-1"
            /> 
            <font-awesome-icon 
                v-else
                icon="envelope-open" 
                size="xl" 
                class="ml-1"
            /> 
        </button>

        <div
            class="z-30 width-300 absolute bg-gray-100 shadow-2xl border-l-2 border-r-2 border-b-2 border-blue-200"
            :class="{
                'hidden': !show
            }"
        >
            <div class="m-2">
                <input
                    class="w-full p-2 bg-white text-base hover:bg-blue-50 focus:bg-blue-100 focus:outline-none focus:ring-2 focus:border-primary ring-inset"
                    placeholder="Find chats by user or chat name"
                    type="text"
                    v-model="searchStr"
                    @keyup="searchInput()"
                >
            </div>

            <div class="h-96">
                <vue-scroll :ops="ops">
                    <div class="flex flex-col space-y-2 ml-1 mr-2">
                        <div v-for="group_id in filteredGroupsIds" :key="group_id">
                            <group-card
                                @click.native="openChatWindow(group_id)"
                                :group_id="group_id"
                            />
                        </div>
                    </div>
                </vue-scroll>
            </div>


            <div v-if="nothingFound" class="m-2 text-red-500">
                {{ nothingFound }}
            </div>
        </div>
    </div>
</template>

<script>
import { mapGetters } from "vuex"
import GroupCard from "./reuseables/GroupCard.vue"
import * as ns from '../../store/module_namespaces.js'

export default {
    
    components: {
        'group-card': GroupCard,
    },

    data(){
        return {
            show: false,
            searchStr: '',
            nothingFound: '',

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
            user: "user",
            filteredGroupsIds: ns.groupsManager() + "/filteredGroupsIds",
            numGroupsWithUnseen: ns.groupsManager() + "/numGroupsWithUnseen",
        }),

        dropdownText(){
            return this.numGroupsWithUnseen > 0 
                ? 'New Messages in ' + this.numGroupsWithUnseen + ' chats'
                : 'Chat groups'
        }, 

    },

    beforeCreate(){  
        
    },

    created(){  
         
    },

    mounted() {
        
    },

    methods:
    {
        manageDropdown() { this.show = !this.show },

        openChatWindow(group_id){
            this.$store.dispatch(ns.groupsManager() + '/openGroup', group_id)
            this.show = false;
        },

        searchInput(){
            this.nothingFound = '';
            this.$store.dispatch(ns.groupsManager() + '/filterGroupsBySearchString', this.searchStr)
            if(!this.filteredGroupsIds.length) this.nothingFound = 'Nothing found :/';
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
