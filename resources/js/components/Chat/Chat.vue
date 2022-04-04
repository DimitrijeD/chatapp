<template>
    <div>
        <!-- Wrapper for chat nav bar-->
        <div class="flex justify-start">
            <create-chat-group
                @createdGroup="getFreshGroupInstance"
            />

            <group-list
                :groupsWithUnseenMessages="groupsWithUnseenMessages"
                :groupIdAcknowledged="groupIdAcknowledged"
                @openWindow="createNewWindow"
            />
        </div>

        <!-- Wrapper for chat windows-->
        <div class="small-container fixed bottom-0 inset-x-0">
            <div class="flex justify-start">
                <chat-window
                    v-for="chat in openedChatWindows"
                    :key="chat.chatGroup.group.id"
                    :chatGroup="chat.chatGroup"
                    :windowIndex="chat.chatGroup.group.id"
                    @closeWindow="closeWindow"
                    @groupAcknowledged = "groupAcknowledged"
                >
                </chat-window>
            </div>
        </div>
    </div>
</template>

<script>

import GroupList from "./GroupList.vue";
import ChatWindow from "./ChatWindow/ChatWindow.vue";
import CreateChatGroup from "./CreateChatGroup.vue";
import { mapGetters } from 'vuex';

export default {
    components:{
        'group-list': GroupList,
        'chat-window': ChatWindow,
        'create-chat-group': CreateChatGroup,
    },

    data(){
        return{
            openedChatWindows: [],
            groupsWithUnseenMessages: [],
            groupIdAcknowledged: null,
        }
    },

    computed: {
        ...mapGetters({ user: "StateUser" }),
    },

    created(){
        this.getInitialUnseenMessagesState();
        this.connectToMessageNotifications();

        axios.get('/api/chat/groups-by-user-without-self-v2')
        .then((res)=>{    
            console.log(res.data);
        })
    },

    methods:
    {
        getInitialUnseenMessagesState()
        {
            axios.get('/api/all-unseen-states')
            .then((res)=>{    
                this.groupsWithUnseenMessages = res.data;
            })
        },

        connectToMessageNotifications()
        {
            Echo.private("App.Models.User." + this.user.id)
            .listen('.message.notification', e => {
                this.handleNewMessageNotifications(e.messageNotification);
            });
        },

        // What to do when receiving new message.
        handleNewMessageNotifications(notification)
        {
            this.messageReceivedButWindowNotOpenYet(notification);
        },

        // When getting window instance from GroupList component (list of chats), data is already loaded
        createNewWindow(chatGroup)
        {
            this.addComponentToArray(chatGroup);
        },

        // New chat group is created and requires additional data,
        // Get fresh instance of chat group,
        getFreshGroupInstance(chatGroup)
        {
            axios.get('/api/chat/group/without-self/' + chatGroup.id)
            .then( res => {
                this.addComponentToArray(res.data);
            });
        },

        // When user sends message in chat, for all users (which participate in that chat) fresh chat window will be opened
        // whether or not they want to see it or not, which could be slightly anoying
        // messageReceivedButWindowNotOpenYet - So open it :)
        messageReceivedButWindowNotOpenYet(notification)
        {
            axios.get('/api/chat/group/with-participants/' + notification.groupId)
            .then(res => {
                this.createNewWindow(res.data);
            });
        },

        addComponentToArray(chatGroup)
        {
            // if chat window is not already open, open it,
            // if window is already open, do not open same window multiple times
            if( ! this.getWindowIndexById(chatGroup.group.id) ){
                this.openedChatWindows.push({
                    'type': 'chat-window',
                    id: chatGroup.group.id,
                    'chatGroup': chatGroup
                });
            }
        },

        // Chat windows are closed by removing chat that element from 'this.openedChatWindows'
        closeWindow(groupId)
        {
            let indexOfTarget = this.getWindowIndexById(groupId);
            if (indexOfTarget) {
                this.openedChatWindows.splice(indexOfTarget, 1);
            }
        },

        getWindowIndexById(groupId)
        {
            for (var index in this.openedChatWindows){
                if (this.openedChatWindows.hasOwnProperty(index) && this.openedChatWindows[index].chatGroup.group.id == groupId) {
                    return index;
                }
            }
            return null;
        },

        groupAcknowledged(groupId)
        {
            this.groupIdAcknowledged = groupId;
        },

    },
}
</script>
