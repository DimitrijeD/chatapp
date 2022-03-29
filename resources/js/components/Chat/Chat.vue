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

        <!-- @todo this is where website content(pages) should be yielded,so chat can exist throughout app -->
        <profile /> 

        <div class="mb-96">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Nulla, nam quis culpa animi natus neque impedit veritatis non tempore, doloribus praesentium temporibus est tenetur facere quae. Harum autem ducimus nihil?</div>
        <div class="mb-96">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Nulla, nam quis culpa animi natus neque impedit veritatis non tempore, doloribus praesentium temporibus est tenetur facere quae. Harum autem ducimus nihil?</div>
        <div class="mb-96">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Nulla, nam quis culpa animi natus neque impedit veritatis non tempore, doloribus praesentium temporibus est tenetur facere quae. Harum autem ducimus nihil?</div>

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
import Profile from '../Profile.vue';

import { mapGetters } from 'vuex';

export default {
    components:{
        'group-list': GroupList,
        'chat-window': ChatWindow,
        'create-chat-group': CreateChatGroup,
        'profile': Profile,
    },

    data(){
        return{
            openedChatWindows: [],
            groupsWithUnseenMessages: [],
            groupIdAcknowledged: null,
        }
    },

    created(){
        
    },

    mounted()
    {

        this.getInitialUnseenMessagesState();
        this.connectToMessageNotifications(this.user.id);
    },

    computed: {
        ...mapGetters({ user: "StateUser" }),
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

        connectToMessageNotifications(id)
        {
            Echo.private("App.Models.User." + id)
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
            axios
            .post('/api/chat/group-without-self/' + chatGroup.id)
            .then( res => {
                this.addComponentToArray(res.data);
            });
        },

        // When user sends message in chat, for all users (which participate in that chat) fresh chat window will be opened
        // whether or not they want to see it or not, which could be slightly anoying
        // messageReceivedButWindowNotOpenYet - So open it :)
        messageReceivedButWindowNotOpenYet(notification)
        {
            axios.post('/api/chat/group-with-participants/' + notification.groupId)
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

        isWindowOpened(groupId)
        {
            for (var index in this.openedChatWindows){
                if (this.openedChatWindows.hasOwnProperty(index) && this.openedChatWindows[index].chatGroup.group.id === groupId) {
                    return index;
                }
            }
            return false;
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
        },

        groupAcknowledged(groupId)
        {
            this.groupIdAcknowledged = groupId;
        },

        // @todo make chat window heading display notification that there are unread meassasges
        notifyIfWindowMinimized(notification)
        {

        },

    },
}
</script>
