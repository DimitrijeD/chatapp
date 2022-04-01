<template>
    <div>
        <button
            @click="manageDropdown()"
            class="width-300 height-44 py-2 pr-2 text-base font-medium border-t-2 border-b-2 border-r-2"
            :class="{
                // When window is open
                'text-white':          showExistingChats,
                'font-medium':         showExistingChats,

                // Doesn't have unseen messages and dropdown is open
                'bg-blue-500':         !newMessage && showExistingChats,
                'border-blue-500':     !newMessage && showExistingChats,

                // Doesn't have unseen messages and dropdown is closed
                'text-blue-500':       !newMessage && !showExistingChats,
                'hover:text-blue-700': !newMessage && !showExistingChats,
                'hover:bg-blue-50':    !newMessage && !showExistingChats,
                'border-blue-300':     !newMessage && !showExistingChats,

                // Has unseen messages and dropdown is open
                'bg-green-500':         newMessage && showExistingChats,
                'border-green-500':     newMessage && showExistingChats,

                // Has unseen messages and dropdown is closed
                'text-green-500':       newMessage && !showExistingChats,
                'hover:text-green-700': newMessage && !showExistingChats,
                'hover:bg-green-50':    newMessage && !showExistingChats,
                'border-green-300':     newMessage && !showExistingChats,
            }"
        >
            {{ dropdownText }}
        </button>


        <div
            v-if="showExistingChats"
            class="z-50 width-300 absolute bg-white border-b-2 border-l-2 border-r-2 border-blue-300 overflow-y-auto h-96"
        >
            <div class="m-2">
                <input
                    class="flex w-full p-2 text-base hover:bg-blue-50 focus:bg-blue-100 focus:outline-none focus:ring-2 focus:border-primary ring-inset"
                    placeholder="Find chats by user"
                    type="text"
                    v-model="userSearchStr"
                    @keyup="searchInput"
                >
            </div>

            <div class="flex flex-col-reverse">
                <div v-for="group in groups" :key="group.id">
                    <div
                        class="p-2 m-2 rounded cursor-pointer"
                        @click="createChatWindow(group)"
                        :class="{
                            // doesnt have unseen messages
                            'bg-blue-50':        !group.hasUnseenState,
                            'hover:bg-blue-100': !group.hasUnseenState,

                            // has unseen messages
                            'bg-green-100':       group.hasUnseenState,
                            'hover:bg-green-200': group.hasUnseenState,

                        }"
                    >
                        <!-- Group Name -->
                        <div class="mb-1">
                            <span v-if="group.group.name" class="p-2 font-medium text-gray-700 text-base ">
                                {{group.group.name}}
                            </span>

                            <span v-else class="p-2 font-medium text-gray-600 text-base">
                                No name
                            </span>
                        </div>

                        <!-- List of all participants in 'this' chat group -->
                        <!-- @todo Need overflow when there are too many participants in list -->
                        <span
                            v-for="participant in group.participants"
                            :key="participant.id"
                            class="bg-blue-400 text-white rounded p-1 m-1"
                        >
                            <span v-if="participant.id !== user.id">
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
    </div>
</template>

<script>
import { mapGetters } from "vuex";

export default {
    props: [
        'groupsWithUnseenMessages',
        'groupIdAcknowledged'
    ],

    data(){
        return {
            showExistingChats: false,
            groupsOriginal: [],
            groups: [],
            userSearchStr: '',
            nothingFound: '',

            dropdownText: 'Chat groups',
            newMessage: false,

            groupsOriginal_v2: [],
            groups_v2: []

        }
    },

    created(){

    },

    computed: {
        ...mapGetters({ user: "StateUser" }),

    },

    mounted() {
        this.getGroupsByUserWithParticipants();
        this.getGroupsByUserWithParticipants_v2();
    },

    watch: {
        groupsWithUnseenMessages:
        {
            handler: function(newValue, oldValue) {

                if(newValue.length){
                    this.dropdownText = 'New Messages in ' + newValue.length + ' chats';
                    this.newMessage = true;
                } else {
                    this.dropdownText = 'Chat groups';
                    this.newMessage = false;
                }

            },
            deep: true
        },

        groupIdAcknowledged:
        {
            handler: function (newValue, oldValue)
            {
                if(newValue){
                    let groupAcknowledgedIndex = this.findGroupById(newValue, this.groups);
                    this.groups[groupAcknowledgedIndex].hasUnseenState = false;

                    let onlyUnseenGroupsIndex = this.findGroupById_forUnseensOnly(newValue, this.groupsWithUnseenMessages);

                    this.groupsWithUnseenMessages.splice(onlyUnseenGroupsIndex, 1);
                }
            }
        },

    },

    methods:{
        manageDropdown()
        {
            this.showExistingChats = !this.showExistingChats;
            // Intent to open window
            if(this.showExistingChats){
                // refresh current array of groups - Intent to open window
                this.getGroupsByUserWithParticipants();
            }
        },

        createChatWindow(group)
        {
            this.$emit('openWindow', group);
            this.showExistingChats = false;
        },

        getGroupsByUserWithParticipants_v2()
        {
            axios.get('/api/chat/groups-by-user-without-self-v2')
                .then((res)=>{
                    this.groupsOriginal_v2 = res.data;
                    this.groups_v2 = this.groupsOriginal_v2;
                    this.addUnseenStateBool_2();
                })
        },

        getGroupsByUserWithParticipants()
        {
            axios
            .get('/api/chat/groups-by-user-without-self')
            .then( res => {
                this.groupsOriginal = res.data;
                this.groups = this.groupsOriginal;
                this.addUnseenStateBool();
            });
        },

        // this.groupsOriginal is constant and is used to filter chat ghroups multiple time (typing and erasing input)
        searchInput()
        {
            this.nothingFound = '';
            // @todo req exp bugs when there are certain characters in string such as '?, *'
            this.groups = this.findGroupsByUser(this.userSearchStr, this.groupsOriginal);

            if(!this.groups.length){
                this.nothingFound = 'Nothing found :/';
            }
        },

        // find all current groups searched user(strSearch - currently typed string into input) appears in
        findGroupsByUser(strSearch, groups)
        {
            let arrOfGroupsUserMightBe = [];

            // grI - group index
            // usI - user index
            for (let grI in groups){
                for (let usI in groups[grI].participants){
                    let first_name = groups[grI].participants[usI].first_name;
                    let last_name = groups[grI].participants[usI].last_name;
                    let groupName = groups[grI].group.name;

                    // 'John Doe Some Group Name'
                    let text = first_name + ' ' + last_name + ' ' + groupName;

                    // If input match anything in this string, return as match
                    if(this.regExpressionMatch(strSearch, text)){
                        arrOfGroupsUserMightBe.push( groups[grI] );
                        // do not push same group multiple times, so: break;
                        break;
                    }
                }
            }
            return arrOfGroupsUserMightBe;
        },

        // Find string in text using case insensitive reg exp
        regExpressionMatch(find, text)
        {
            let regex = new RegExp(find, 'i');
            return text.match(regex);
        },

        addUnseenStateBool()
        {
            for(let grUnseenInd in this.groupsWithUnseenMessages){
                if(this.groupsWithUnseenMessages.hasOwnProperty(grUnseenInd)){

                    for(let grAllInd in this.groups){
                        if(this.groups.hasOwnProperty(grAllInd) && (this.groupsWithUnseenMessages[grUnseenInd].id === this.groups[grAllInd].group.id) ){
                            this.groups[grAllInd].hasUnseenState = true;
                            break;
                        }
                    }

                }
            }
        },

        addUnseenStateBool_2()
        {
            for(let grUnseenInd in this.groupsWithUnseenMessages){
                if(this.groupsWithUnseenMessages.hasOwnProperty(grUnseenInd)){

                    for(let grAllInd in this.groups_v2){
                        if(this.groups_v2.hasOwnProperty(grAllInd) && (this.groupsWithUnseenMessages[grUnseenInd].id === this.groups_v2[grAllInd].id) ){
                            this.groups_v2[grAllInd].hasUnseenState = true;
                            break;
                        }
                    }

                }
            }
        },

        findGroupById(id, arrOfGroups){
            for(let index in arrOfGroups){
                if(arrOfGroups.hasOwnProperty(index) && arrOfGroups[index].group.id == id){
                    return index;
                }
            }
        },

        findGroupById_forUnseensOnly(id, arrOfGroups){
            for(let index in arrOfGroups){
                if(arrOfGroups.hasOwnProperty(index) && arrOfGroups[index].id == id){
                    return index;
                }
            }
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
</style>
