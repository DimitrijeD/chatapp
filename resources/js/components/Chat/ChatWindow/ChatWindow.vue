<template>
    <transition name="slide-fade-chat-window">
        <div class="window-x flex-none mr-3 box-border"
            :class="{ 'flex flex-col-reverse': isMinimized }"
        >
            <!-- Chat Window Header -->
            <div class="flex flex-nowrap gap-2 h-16 bg-blue-500">
                <chat-participants
                    :group="group"
                />
                <window-management
                    :group_id="group.id"
                    :showConfig="showConfig"
                    :isMinimized="isMinimized"
                    @minimizeWindow="minimizeWindow()"
                    @openConfig="openConfig()"
                />
            </div>
            <!-- -------------------- -->


            <!-- wrapper for dynamic window minimization -->
            <div
                class="static flex not-header-y flex-col border-l-2 border-r-2 border-b-2 border-blue-400"
                :class="{ 'hidden': isMinimized }"
                @click="selfAcknowledged();"
            >
                <config 
                    v-if="showConfig"
                    class="window-x-minus-border h-full"
                    :group="group"
                    :permissions="permissions"
                    :chatRole="chatRole"
                    :roles="roles"
                />

                <!-- Chat Window Body -->
                <div class="body-y">
                    <messages-block
                        :group="group"
                    />
                </div>
                <!-- -------------------- -->


                <!-- Chat Window Footer -->
                <div 
                    v-if="permissions.send_message" 
                    class="border-t-4 border-gray-200 bg-gray-100"
                >
                    <message-input
                        :group_id="group.id"
                    />
                </div>
                <!-- -------------------- -->
            </div>
        </div>
    </transition>
</template>

<script>
import WindowManagement from "./Header/WindowManagement.vue"
import ChatParticipants from "./Header/ChatParticipants.vue"
import MessagesBlock    from "./Body/MessagesBlock"
import Config           from "./Body/Config.vue"
import MessageInput     from "./Footer/MessageInput.vue"

import { mapGetters } from 'vuex'

export default {
    props: [
        'group_id',
    ],

    components: {
        'chat-participants': ChatParticipants,
        'messages-block': MessagesBlock,
        'message-input': MessageInput,
        'window-management': WindowManagement,
        'config': Config,
    },

    data(){
        return {
            isMinimized: false,
            lastAcknowledgedMessageId: null,
            showConfig: false,
            permissions: {},
        }
    },

    computed: {
        ...mapGetters({ 
            user: "StateUser",
            rules: "chat_rules/StateRules",
            roles: "chat_rules/StateRoles",
            actionKeys: "chat_rules/StateKeys",
        }),

        group(){ 
            return this.$store.getters['groups/getGroupById'](this.group_id)
        },

        chatRole(){ 
            return this.$store.getters['groups/getUserRole']({ 
                group_id: this.group.id, 
                user_id: this.user.id
            })
        }
    },

    watch: {
        chatRole: function () {
            this.createPermissions()
            // call event here: User, ur role has been changed
        },
    },

    created() {
        this.createPermissions()

        this.listenForNewMessages()
        this.listenForMessagesSeen()
        this.listenForParticipantRoleChange()
        this.listenForUserRemoved()
        this.listenForUserAdded()
        this.listenForUserLeftGroup()
        this.listenForGroupNameChange()
    },

    mounted(){

    },

    methods: {   
        minimizeWindow(){
            this.isMinimized = !this.isMinimized
        },

        openConfig(){
            this.showConfig = !this.showConfig
        },

        getMessages(){
            this.$store.dispatch('groups/getMessages', {group_id: this.group.id })
        },

        findLatestMessageId: (messages) => Math.max(...Object.keys(messages)),

        isUserOwnerOfLastMessage: (message, userId) => message.user_id == userId ? true : false,

        // Event when user clicks his own window (he saw/red all messages)
        selfAcknowledged()
        {
            // Preventing user from acknowledging chat group with no messages. Whould be cool if
            // this was called only once (when there accualy are no messages)
            if(!Object.keys(this.group.messages).length) return 

            if(this.group?.hasUnseenState)
                this.messageSeen()

            // Preventing user from acknowledging his own message.
            if(this.isUserOwnerOfLastMessage(this.group.messages[this.findLatestMessageId(this.group.messages)], this.user.id)) return

            // or if user already acknowledged all messages.
            if(this.group.last_msg_id == this.lastAcknowledgedMessageId) return
            
            this.messageSeen()
        },

        messageSeen()
        {
            this.lastAcknowledgedMessageId = this.group.last_msg_id

            this.$store.dispatch('groups/setAllMessagesAcknowledged', {
                group_id: this.group.id,
                lastAcknowledgedMessageId: this.lastAcknowledgedMessageId
            })
        },

        listenForNewMessages()
        {
            this.getMessages()

            Echo.private("group." + this.group.id)
            .listen('.message.new', e => {
                this.getMessages()
            })
        },

        listenForMessagesSeen()
        {
            Echo.private("group." + this.group.id)
            .listen('.message.seen', e => {
                this.$store.dispatch('groups/seenEvent', e.seenData)
            });
        },

        listenForParticipantRoleChange()
        {
            Echo.private("group." + this.group.id)
            .listen('.participant.role.change', e => {
                this.$store.dispatch('groups/patchParticipantRole', e.data)
            });
        },

        listenForUserRemoved()
        {
            Echo.private("group." + this.group.id)
            .listen('.participant.removed', e => {
                e.data.removed_user_id == this.user.id
                    ? this.$store.dispatch('groups/clearGroupData', e.data)
                    : this.$store.dispatch('groups/removedParticipantEvent', e.data)
            });
        },

        listenForUserAdded()
        {
            Echo.private("group." + this.group.id)
            .listen('.participant.added', e => {
                let isAmongThem = false

                for(let i in e.data.addedUsers){
                    if(e.data.addedUsers[i].user_id == this.user.id){
                        isAmongThem = true
                        break
                    }
                }

                isAmongThem
                    ? console.log("error, ChatWindow::listenForUserAdded() - user listens for event which he shouldn't be listening to since he was no longer participant in chat group.")  
                    : this.$store.dispatch('groups/addedParticipantEvent', e.data)
            });
        },

        listenForUserLeftGroup()
        {
            Echo.private("group." + this.group.id)
            .listen('.participant.left', e => {
                this.$store.dispatch('groups/participantLeftGroupEvent', e.data)
            });
        },

        listenForGroupNameChange()
        {
            Echo.private("group." + this.group.id)
            .listen('.group.new_name', e => {
                this.$store.dispatch('groups/changeGroupNameEvent', e.data)
            });
        },

        createPermissions()
        {
            this.permissions = this.createObjectFromArrayValues(this.actionKeys)

            this.permission_canAdd()
            this.permission_canRemove()
            this.permission_canChangeRole()
            this.permission_canSendMessage()
            this.permission_canChangeGroupName()
        },

        permission_canAdd()
        {
            let action = 'add'
            this.ruleDepth3(this.rules[action][this.chatRole], action)
        },

        permission_canRemove()
        {
            let action = 'remove'
            this.ruleDepth3(this.rules[action][this.chatRole], action)
        },

        permission_canChangeRole()
        {
            let action = 'change_role'
            this.ruleDepth4(this.rules[action][this.chatRole], action)
        },


        permission_canSendMessage()
        {
            let action = 'send_message'
            this.ruleDepth2(this.rules[action][this.chatRole], action)
        },

        permission_canChangeGroupName()
        {
            let action = 'change_group_name'
            this.ruleDepth2(this.rules[action][this.chatRole], action)
        },

        ruleDepth2(level1, action)
        {
            this.permissions[action] = level1[this.group.model_type] ? true : false
        },

        ruleDepth3(level1, action)
        {
            for(let targetRole in level1){
                if(level1[targetRole][this.group.model_type])
                    this.permissions[action].push(targetRole)
            }
        },

        ruleDepth4(level1, action)
        {
            this.permissions[action] = {}

            for(let fromRole in level1){
                for(let toRole in level1[fromRole]){
                    if(level1[fromRole][toRole][this.group.model_type] ){
                        
                        if(!this.permissions[action][fromRole]){
                            this.permissions[action][fromRole] = []
                        }

                        this.permissions[action][fromRole].push(toRole)
                    }
                }
            }
        },

        createObjectFromArrayValues(array)
        {
            let object = {}

            for(let i = 0; i < array.length; i++){
                object[array[i]] = []
            }

            return object
        },

    }
}

</script>

<style>
.window-x{
    width: 464px;
}

.window-x-minus-border{
    width: 461px;
}

.window-y{
    height: 588.5px;
}

.not-header-y{
    height: 588.5px;
}

.body-y {
    height: 496px;
}

</style>
