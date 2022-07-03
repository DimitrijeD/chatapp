<template>
    <div>
        <button
            @click="showDropdown()"
            class="width-300 height-44 py-2 pr-2 text-xl text-blue-50 font-medium"
            :class="{
                'bg-blue-800 hover:bg-blue-600': showCreateDropdown,
                'hover:bg-blue-800': !showCreateDropdown,
            }"
        >
            New chat
            <font-awesome-icon 
                icon="people-line" 
                size="lg" 
                class="ml-1"
            /> 
        </button>

        <transition name="slide-fade-nav-dropdowns">
            <div
                v-if="showCreateDropdown"
                class="width-300 z-50 absolute bg-gray-100 shadow-2xl border-l-2 border-r-2 border-blue-200"
            >      
                <div class="m-2">
                    <input
                        class="a-input focus:outline-none focus:ring-2 focus:border-primary ring-inset"
                        placeholder="Name of new group chat"
                        type="text"
                        v-model="newChatGroup.name"
                    >
                </div>

                <!-- Chose group type -->
                <div class="m-2">
                    <select 
                        class="text-gray-400 a-input focus:outline-none focus:ring-2 focus:border-primary ring-inset" 
                        v-model="selected_model_type"
                    >
                        <option 
                            v-for="(type, index) in getHumanReadableGroupTypes" 
                            v-bind:value="type.value" 
                            :key="index"
                        >
                            {{ type.text }}
                        </option>
                    </select>
                </div>

                <div class="m-2">
                    <search-input 
                        :actions="input.actions"
                        :exclude="[]"
                        :placeholder="input.placeholder"
                    />
                </div>

                <div class="user-list-height select-none m-2">
                    <vue-scroll :ops="ops">
                        <ul> 
                            <li v-for="(id, index) in users" :key="index">
                                <small-user 
                                    :user="getUser(id)"
                                    @click.native="selectOrDeseceltUser(id)"
                                    :layoutCls="isUserSelected(id) ? 'text-white bg-blue-400 space-x-2 py-1' : 'text-blue-500 bg-white hover:bg-red-50 space-x-2 py-1'"
                                    class="m-1 mr-3"
                                /> 
                            </li>
                        </ul>
                    </vue-scroll>
                </div>

                <div v-for="(error, index) in errors" class="text-danger" :key="index">
                    {{ error }}
                </div>

                <div
                    class="text-base mt-2 p-2 text-center cursor-pointer"
                    :class="{
                        'bg-blue-500 hover:bg-blue-600 text-white':  canCreate,
                        'disabled text-gray-600 bg-gray-200':       !canCreate,
                    }"
                    @click="createNewChatGroup()"
                >
                    Create chat group
                </div>
            </div>

        </transition>
    </div>
</template>

<script>
import { mapGetters } from 'vuex';
import SearchInput from './reuseables/SearchInput.vue'
import SmallUser from './reuseables/SmallUser.vue';

export default {
    components: {
        'search-input': SearchInput,
        'small-user': SmallUser,
    },

    data() {
        return {
            showCreateDropdown: false,
            newChatGroup: {
                name: '',
                users_ids: [],
                model_type: '',
            },
            errors: [],
            nothingFound: '',

            selected_model_type: 'PRIVATE',
            defaultNewGroupType: 'PRIVATE',

            input: {
                actions: {
                    api: 'searchForAddUsersInApi',
                    store: 'searchForAddUsersInStore'
                },
                placeholder: "Find users to add",
            },

            ops: {
                scrollPanel: {
                    scrollingX: false
                },
                rail: {
                    background: '#BFDBFF',
                    opacity: 0.5,
                    size: '6px',
                },
                bar: {
                    keepShow: true
                }
            },
        }
    },

    computed: {
        ...mapGetters({ 
            user: "StateUser",
            groupTypes: "chat_rules/StateGroupTypes",
        }),

        getHumanReadableGroupTypes()
        {
            let groupTypes = []

            for(let i in this.groupTypes){
                groupTypes.push({
                    text: this.capitalizeFirstLetter(this.groupTypes[i].replace("_", " ").toLowerCase()),
                    value: this.groupTypes[i]
                })
            }

            return groupTypes
        },

        users(){ return this.$store.getters['users/getFilterForAddUsers'] },

        canCreate(){
            return this.newChatGroup.users_ids.length
        }

    },

    created(){
        
    },

    mounted()
    {

    },

    methods:
    {        
        capitalizeFirstLetter(string) {
           return string.charAt(0).toUpperCase() + string.slice(1);
        },

        showDropdown(){
            this.newChatGroup = {
                name: '',
                users_ids: [],
                model_type: '',
            }

            this.errors = []

            this.showCreateDropdown = !this.showCreateDropdown
        },

        createNewChatGroup()
        {
            this.checkForErrors()

            if(this.errors.length > 0) return

            this.resolveGroupParams()
            
            this.$store.dispatch('groups/storeGroup', this.newChatGroup)

            this.resetComponentVars()
        },

        checkForErrors()
        {
            // reset errors before pushing new messages
            this.errors = []

            if(this.newChatGroup.users_ids.length === 0){
                this.errors.push('Select at least one user')
            }
            return this.errors
        },

        resetComponentVars()
        {
            this.newChatGroup = {
                name: '',
                users_ids: [],
                model_type: '',
            }
            this.errors = []
            this.showCreateDropdown = false
        },

        resolveGroupParams()
        {
            this.newChatGroup.users_ids.push(this.user.id)
            this.resolveGroupType()
        },

        resolveGroupType()
        {
            if(this.selected_model_type == "PRIVATE" && this.newChatGroup.users_ids.length > 2){
                this.newChatGroup.model_type = "PROTECTED" 
                return
            }

            this.newChatGroup.model_type = this.selected_model_type         
                ? this.selected_model_type 
                : this.defaultNewGroupType

        },

        getUser(id) { return this.$store.getters['users/getById'](id)},

        selectOrDeseceltUser(id)
        {
            if(this.newChatGroup.users_ids.includes(id)){
                this.newChatGroup.users_ids.splice(this.newChatGroup.users_ids.indexOf(id), 1)
            } else {
                this.newChatGroup.users_ids.push(id)
            }
        },

        isUserSelected(id)
        {
            return this.newChatGroup.users_ids.includes(id)
        }
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

.user-list-height{
    height: 350px;
}

.a-input {
    @apply w-full p-3 text-base bg-white font-light text-gray-700;
}
</style>
