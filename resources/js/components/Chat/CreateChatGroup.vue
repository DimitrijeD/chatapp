<template>
    <div>
        <button
            @click="showDropdown()"
            class="width-300 height-44 py-2 pr-2 text-base font-medium"
            v-bind:class="{
                'bg-blue-500': showCreateDropdown,
                'text-white': showCreateDropdown,

                'text-blue-500':       !showCreateDropdown,
                'hover:text-blue-700': !showCreateDropdown,
                'hover:bg-blue-50':    !showCreateDropdown,
                'border-2':            !showCreateDropdown,
                'border-blue-300':     !showCreateDropdown,
            }"
        >
            Create new chat group
        </button>

        <div
            v-if="showCreateDropdown"
            class="width-300 z-50 absolute bg-white border-b-2 border-l-2 border-r-2 border-blue-300"
        >

            <div class="m-2">
                <input
                    class="flex w-full p-2 text-base focus:bg-blue-50 focus:outline-none focus:ring-2 focus:border-primary ring-inset"
                    placeholder="Name of new group chat"
                    type="text"
                    v-model="newChatGroup.name"
                >
            </div>

            <!-- Chose group type -->
            <div class="m-2">
                <select class="w-full p-2 bg-gray-50 focus:bg-blue-50 focus:outline-none focus:ring-2 focus:border-primary ring-inset" v-model="selected_model_type">
                    <option v-for="(model_type, index) in model_types" v-bind:value="model_type.value" :key="index">
                        {{ model_type.text }}
                    </option>
                </select>
            </div>

            <div class="m-2">
                <input
                    class="flex w-full p-2 text-base focus:bg-blue-50 focus:outline-none focus:ring-2 focus:border-primary ring-inset"
                    placeholder="Find people to chat"
                    type="text"
                    @keyup="searchInput"
                    v-model="userSearchStr"
                >
            </div>

            <div class="overflow-y-auto user-list-height">
                <div v-for="user in this.allUsers" class="flex flex-col m-1 cursor-pointer" :key="user.id">
                    <div class="flex flex-row">

                        <!-- img -->
                        <div class="w-20 h-20">
                            <img
                                :src="user.thumbnail"
                                alt="no img :/"
                                class="object-cover relative border border-gray-100 shadow-sm h-full"
                            >
                        </div>

                        <!-- UserName -->
                        <div class="grid grid-cols-1 gap-0 place-content-center">
                            <p
                                @click="selectedUserForChat(user)"
                                class="ml-2 rounded "
                                v-bind:class="{
                                    'text-blue-500': !user.selectionStatus,
                                    'bg-white': !user.selectionStatus,

                                    'text-white':    user.selectionStatus,
                                    'bg-blue-400':   user.selectionStatus,
                                    'font-semibold': user.selectionStatus,
                                }"
                            >
                                {{ user.first_name }} {{ user.last_name }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div v-model="errors" v-for="(error, index) in errors" class="text-danger" :key="index">
                {{ error }}
            </div>

            <div
                class="bg-blue-400 hover:bg-blue-500 text-white text-base mt-2 p-2 text-center cursor-pointer"
                @click="createNewChatGroup()"
            >
                Create chat group
            </div>

        </div>

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
                model_type: ''
            },
            errors: [],
            nothingFound: '',
            userSearchStr: '',

            selected_model_type: 'PRIVATE',
            selected_chatting_type: 'PRIVATE',

            model_types: [
                { text: 'Private chat', value: 'PRIVATE' },
                { text: 'Protected chat', value: 'PROTECTED' },
                { text: 'Public chat', value: 'PUBLIC' }
            ]
        }
    },

    created(){
        
    },

    computed: {
        ...mapGetters({ user: "StateUser" }),

    },

    mounted()
    {
        this.getAllUsersExceptSelf();

    },

    methods:
    {        
        showDropdown(){
            for(let userIndex in this.allUsers){
                this.allUsers[userIndex].selectionStatus = false;
            }
            this.newChatGroup = {
                name: '',
                users: [],
            };
            this.errors = null;

            this.showCreateDropdown = !this.showCreateDropdown;
        },

        getAllUsersExceptSelf()
        {
            axios.get('/api/chat/users/without-self')
            .then((res) => {
                this.allUsersOriginal = this.addSelectedStatusToAllUsers(res.data);
                this.allUsers = this.allUsersOriginal;
            });
        },

        addSelectedStatusToAllUsers(users)
        {
            for(let i = 0; i < users.length; i++){
                users[i].selectionStatus = false;
            }
            return users;
        },

        // this user is chosen for new chat group, change color of user to blue and add it to newChatGroup
        // if user is already chosen and then clicked again, remove it from newChatGroup and set user style in list to default
        selectedUserForChat(user)
        {
            if( !this.checkIfUserAlreadySelected(user) )
            {
                this.newChatGroup.users.push(user);
                this.allUsers[ this.findArrIndexByUserId(this.allUsers, user.id) ].selectionStatus = true;
            } else {
                const index = this.getArrayIndexFromElement(this.newChatGroup.users, user);
                if (index > -1) {
                    this.newChatGroup.users.splice(index, 1);
                }
                this.allUsers[ this.findArrIndexByUserId(this.allUsers, user.id) ].selectionStatus = false;
            }
        },

        createNewChatGroup()
        {
            this.checkForErrors();
            if(this.errors.length > 0){
                return;
            }

            // add self
            this.newChatGroup.users.push(this.user);
            this.newChatGroup.model_type = this.resolveNewGroupType(this.selected_model_type);
            this.newChatGroup.chatting_type = this.resolveNewChattingType(this.selected_chatting_type);

            axios
            .post('/api/chat/group/store', this.newChatGroup)
            .then( res => {
                this.$emit('createdGroup', res.data);
            });

            this.resetComponentVars();
        },

        // return true if user is already selected
        checkIfUserAlreadySelected(user)
        {
            for(let i = 0; i < this.newChatGroup.users.length; i++){
                if( this.newChatGroup.users[i].id === user.id ){
                    return true;
                }
            }
            return false;
        },

        checkForErrors()
        {
            // reset errors before pushing new messages
            this.errors = [];

            if(this.newChatGroup.users.length === 0){
                this.errors.push('Select at least one user');
            }
            return this.errors;
        },

        resetComponentVars()
        {
            this.newChatGroup = {
                name: '',
                users: [],
            };
            this.showCreateDropdown = false;
        },

        findArrIndexByUserId(users, id)
        {
            for(let i = 0; i < users.length; i++){
                if(users[i].id == id){
                    return i;
                }
            }
            // user with that Id does not exist in this array of users
            return false;
        },

        getArrayIndexFromElement(array, element){
            return array.indexOf(element);
        },

        // this.groupsOriginal is constant and is used to filter chat ghroups multiple time (typing and erasing input)
        searchInput()
        {
            this.nothingFound = '';
            // @todo req exp bugs when there are certain characters in string such as '?, *'
            this.allUsers = this.findUsersBySearch(this.userSearchStr, this.allUsersOriginal);

            if(!this.allUsers.length){
                this.nothingFound = 'Nothing found :/';
            }
        },

        // find all current groups searched user(strSearch - currently typed string into input) appears in
        findUsersBySearch(strSearch, users)
        {
            let arrOfSearchMatchedUsers = [];

            for (let userInd in users){
                let first_name = users[userInd].first_name;
                let last_name = users[userInd].last_name;

                let text = first_name + ' ' + last_name;

                // If input match anything in this string, return as match
                if(this.regExpressionMatch(strSearch, text)){
                    arrOfSearchMatchedUsers.push( users[userInd] );
                }
            }
            return arrOfSearchMatchedUsers;
        },

        // Find string in text using case insensitive reg exp
        regExpressionMatch(find, text)
        {
            let regex = new RegExp(find, 'i');
            return text.match(regex);
        },

        resolveNewGroupType(type)
        {
            if(!type){
                return "PRIVATE";    
            }
            return type;
        },

        resolveNewChattingType(type)
        {
            if(!type){
                return "PRIVATE";    
            }
            return type;
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
</style>
