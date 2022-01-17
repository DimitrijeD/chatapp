<template>
    <div>
        <!-- Wrapper for chat nav bar-->
        <div class="flex justify-start">
            <create-chat-group
                @createdGroup="getFreshGroupInstance"
            />

            <group-list
                :userSelf="user"
                :groupsWithUnseenMessages="groupsWithUnseenMessages"
                :groupIdAcknowledged="groupIdAcknowledged"
                @openWindow="createNewWindow"
            />
        </div>

        <!-- @todo this is where website content(pages) should be yielded,so chat can exist throughout app -->
        {{ this.$store.state.userX }}
        <profile />

        <div >
            <p class="mb-96">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eum excepturi expedita fugit, hic iste,
                laboriosam laborum libero placeat quis, quod recusandae veniam! Asperiores excepturi maiores natus odit,
                optio perferendis reprehenderit!</p>

            <p class="mb-96">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eum excepturi expedita fugit, hic iste,
                laboriosam laborum libero placeat quis, quod recusandae veniam! Asperiores excepturi maiores natus odit,
                optio perferendis reprehenderit!</p>
            <p class="mb-96">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eum excepturi expedita fugit, hic iste,
                laboriosam laborum libero placeat quis, quod recusandae veniam! Asperiores excepturi maiores natus odit,
                optio perferendis reprehenderit!</p>

            <p class="mb-96">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eum excepturi expedita fugit, hic iste,
                laboriosam laborum libero placeat quis, quod recusandae veniam! Asperiores excepturi maiores natus odit,
                optio perferendis reprehenderit!</p>
            <p class="mb-96">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eum excepturi expedita fugit, hic iste,
                laboriosam laborum libero placeat quis, quod recusandae veniam! Asperiores excepturi maiores natus odit,
                optio perferendis reprehenderit!</p>

            <p class="mb-96">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eum excepturi expedita fugit, hic iste,
                laboriosam laborum libero placeat quis, quod recusandae veniam! Asperiores excepturi maiores natus odit,
                optio perferendis reprehenderit!</p>
        </div>

        <!-- Wrapper for chat windows-->
        <div class="container mx-auto fixed bottom-0 inset-x-0">
            <div class="flex justify-start">
                <component
                    v-for="chat in openedChatWindows"
                    :is="chat.type"
                    :key="chat.chatGroup.group.id"
                    :chatGroup="chat.chatGroup"
                    :userSelf="user"
                    :windowIndex="chat.chatGroup.group.id"
                    @closeWindow="closeWindow"
                    @groupAcknowledged = "groupAcknowledged"
                >
                </component>
            </div>
        </div>
    </div>
</template>

<script>

import GroupList from "./GroupList.vue";
import ChatWindow from "./ChatWindow/ChatWindow.vue";
import CreateChatGroup from "./CreateChatGroup.vue";
import Profile from'../Profile.vue';

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
            user: null,
            groupsWithUnseenMessages: [],
            groupIdAcknowledged: null,
        }
    },

    created() {
        // console.log('created - Chat.vue', this.$store.state.userX);
    },

    mounted()
    {
        this.getUserSelf();
        this.getInitialUnseenMessagesState();
        // console.log('mounted - Chat.vue', this.$store.state.userX);
    },

    beforeUpdate(){
        // console.log('BeforeUpdate - Chat.vue', this.$store.state.userX);
    },

    methods:
    {
        getUserSelf()
        {
            axios.get('/api/user')
            .then((res) => {
                this.user = res.data;
                this.connectToMessageNotifications(this.user.id);
            });
        },

        getInitialUnseenMessagesState()
        {
            axios.get('/api/all-unseen-states')
            .then((res)=>{
                // console.log(res.data);
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
            // console.log('new message,seen');
            // console.log(groupId);
        },

        // @todo make chat window heading display notification that there are unread meassasges
        notifyIfWindowMinimized(notification)
        {

        },



    },
}
</script>
