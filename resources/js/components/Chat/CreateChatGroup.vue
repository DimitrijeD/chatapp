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
                class="width-300 z-50 absolute bg-gray-100 mt-1 shadow-2xl"
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
                    <input
                        class="a-input focus:outline-none focus:ring-2 focus:border-primary ring-inset"
                        placeholder="Search for users"
                        type="text"
                        @keyup="searchInput"
                        v-model="userSearchStr"
                    >
                </div>

                <div class=" user-list-height select-none pt-1">
                    <vue-scroll>
                        <div 
                            v-for="user in this.allUsers" 
                            class="flex flex-col m-2 cursor-pointer " 
                            @click="selectedUserForChat(user)"
                            :key="user.id"
                        >
                            <div 
                                :class="{
                                    'text-blue-500 bg-white hover:bg-red-50': !user.selectionStatus,
                                    'text-white bg-blue-400 font-semibold': user.selectionStatus,
                                }">
                                
                                <img
                                    :src="user.thumbnail"
                                    alt="no img :/"
                                    class="w-16 h-16 inline-block m-2 object-cover border border-gray-100 rounded-full"
                                >

                                <p class="ml-2 inline-block">
                                    {{ user.first_name }} {{ user.last_name }}
                                </p>
                            </div>
                        </div>
                    </vue-scroll>
                </div>

                <div v-for="(error, index) in errors" class="text-danger" :key="index">
                    {{ error }}
                </div>

                <div
                    class="bg-blue-500 hover:bg-blue-600 text-white text-base mt-2 p-2 text-center cursor-pointer"
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

export default {

    data() {
        return {
            allUsers: [],
            showCreateDropdown: false,
            newChatGroup: {
                name: '',
                users: [],
                model_type: '',
            },
            errors: [],
            nothingFound: '',
            userSearchStr: '',

            selected_model_type: 'PRIVATE',

            model_types: [
                { text: 'Private chat', value: 'PRIVATE' },
                { text: 'Protected chat', value: 'PROTECTED' },
                { text: 'Public chat', value: 'PUBLIC_OPEN' },
                { text: 'Public closed chat', value: 'PUBLIC_CLOSED' }
            ],

            defaultNewGroupType: 'PRIVATE',
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
        }

    },

    created(){
        
    },

    mounted()
    {
        this.getAllUsersExceptSelf()

    },

    methods:
    {        
        capitalizeFirstLetter(string) {
           return string.charAt(0).toUpperCase() + string.slice(1);
        },

        showDropdown(){
            for(let userIndex in this.allUsers){
                this.allUsers[userIndex].selectionStatus = false
            }

            this.newChatGroup = {
                name: '',
                users: [],
                model_type: '',
            }

            this.errors = []

            this.showCreateDropdown = !this.showCreateDropdown
        },

        getAllUsersExceptSelf()
        {
            axios.get('/api/chat/users/without-self')
            .then((res) => {
                this.allUsersOriginal = this.addSelectedStatusToAllUsers(res.data)
                this.allUsers = this.allUsersOriginal
            });
        },

        addSelectedStatusToAllUsers(users)
        {
            for(let i = 0; i < users.length; i++){
                users[i].selectionStatus = false
            }
            return users
        },

        // this user is chosen for new chat group, change color of user to blue and add it to newChatGroup
        // if user is already chosen and then clicked again, remove it from newChatGroup and set user style in list to default
        selectedUserForChat(user)
        {
            if( !this.checkIfUserAlreadySelected(user) )
            {
                this.newChatGroup.users.push(user)
                this.allUsers[ this.findArrIndexByUserId(this.allUsers, user.id) ].selectionStatus = true
            } else {
                const index = this.getArrayIndexFromElement(this.newChatGroup.users, user)
                if (index > -1) {
                    this.newChatGroup.users.splice(index, 1)
                }
                this.allUsers[ this.findArrIndexByUserId(this.allUsers, user.id) ].selectionStatus = false
            }
        },

        createNewChatGroup()
        {
            this.checkForErrors()

            if(this.errors.length > 0) return

            this.resolveGroupParams()

            this.$store.dispatch('groups/storeGroup', this.newChatGroup)

            this.resetComponentVars()
        },

        // return true if user is already selected
        checkIfUserAlreadySelected(user)
        {
            for(let i = 0; i < this.newChatGroup.users.length; i++){
                if( this.newChatGroup.users[i].id === user.id ){
                    return true
                }
            }
            return false
        },

        checkForErrors()
        {
            // reset errors before pushing new messages
            this.errors = []

            if(this.newChatGroup.users.length === 0){
                this.errors.push('Select at least one user')
            }
            return this.errors
        },

        resetComponentVars()
        {
            this.newChatGroup = {
                name: '',
                users: [],
                model_type: '',
            }
            this.errors = []
            this.showCreateDropdown = false
        },

        findArrIndexByUserId(users, id)
        {
            for(let i = 0; i < users.length; i++){
                if(users[i].id == id){
                    return i
                }
            }
            // user with that Id does not exist in this array of users
            return false
        },

        getArrayIndexFromElement(array, element){
            return array.indexOf(element)
        },

        // this.groupsOriginal is constant and is used to filter chat groups multiple times (typing and erasing input)
        searchInput()
        {
            this.nothingFound = ''
            // @todo req exp bugs when there are certain characters in string such as '?, *'
            this.allUsers = this.searchForUsers(this.userSearchStr, this.allUsersOriginal)

            if(!this.allUsers.length){
                this.nothingFound = 'Nothing found :/'
            }
        },

        // find all users which match string strSearch
        searchForUsers(strSearch, users)
        {
            let arrOfSearchMatchedUsers = []

            for (let userInd in users){
                let first_name = users[userInd].first_name
                let last_name = users[userInd].last_name

                let text = first_name + ' ' + last_name

                // If input match anything in this string, return as match
                if(this.regExpressionMatch(strSearch, text)){
                    arrOfSearchMatchedUsers.push( users[userInd] )
                }
            }
            return arrOfSearchMatchedUsers
        },

        // Find string in text using case insensitive reg exp
        regExpressionMatch(find, text)
        {
            let regex = new RegExp(find, 'i')
            return text.match(regex)
        },

        resolveGroupParams()
        {
            this.newChatGroup.users.push(this.user)

            this.newChatGroup.model_type = this.selected_model_type 
                ? this.selected_model_type 
                : this.defaultNewGroupType

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

.user-list-height{
    height: 350px;
}

.a-input {
    @apply w-full p-3 text-base bg-white font-light text-gray-700;
}
</style>
