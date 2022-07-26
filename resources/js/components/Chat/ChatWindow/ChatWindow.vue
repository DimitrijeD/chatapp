<template>
    <div class="window-x flex-none mr-3 box-border"
        :class="{ 'flex flex-col-reverse': isMinimized }"
    >
        <!-- Chat Window Header -->
        <div class="flex flex-nowrap gap-2 h-16 bg-blue-500">
            <chat-participants :group="group" />

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
            @click="selfAcknowledged()"
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
</template>

<script>
import WindowManagement from "./Header/WindowManagement.vue"
import ChatParticipants from "./Header/ChatParticipants.vue"
import MessagesBlock    from "./Body/MessagesBlock"
import Config           from "./Body/Config.vue"
import MessageInput     from "./Footer/MessageInput.vue"

import * as ns from '../../../store/module_namespaces.js'
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
            showConfig: false,
            permissions: {},
            config: {
                refreshGroupOnLoad: false
            },
            gm_ns: ns.groupModule(this.group_id), // group module name space
        }
    },

    computed: 
    {
        ...mapGetters({ 
            user: "user",
            rules:      ns.chat_rules() + "/StateRules",
            roles:      ns.chat_rules() + "/StateRoles",
            actionKeys: ns.chat_rules() + "/StateKeys",
        }),

        group(){ return this.$store.getters[this.gm_ns + '/state']},

        chatRole(){ return this.$store.getters[this.gm_ns + '/getUserRole'](this.user.id) },

        last_message(){  return this.$store.getters[`${this.gm_ns}/last_message`] },

        seen(){ return this.$store.getters[`${this.gm_ns}/seen`]},
    },

    watch: {
        chatRole: function () {
            this.createPermissions()
            // call event here: User, ur role has been changed
        },
    },

    created() 
    {
        this.createPermissions()
        // this.initGroup()

        this.getInitMessages()
        this.listenForMessagesSeen()
        this.listenForParticipantRoleChange()
        this.listenForParticipantRemoved()
        this.listenForParticipantAdded()
        this.listenForUserLeftGroup()
        this.listenForGroupNameChange()
    },

    beforeDestroy() {
        // Echo.private('group.' + this.group_id)
        //     .stopListening('.message.seen')
    },

    mounted(){

    },

    methods: {   
        selfAcknowledged(){
            if(this.seen) return

            if(this.last_message.user_id == this.user.id){
                // console.log('CW, seen state is false meaning I didnt see last msg. this is shown because last message owner is me.')
                // console.log('this will cause FE issue:')
                // console.log('bg-green but messages are seen, and I did click Chat Window')
                return
            }

            this.$store.dispatch(this.gm_ns + '/allMessagesSeen', this.last_message.id)
        },

        minimizeWindow(){ this.isMinimized = !this.isMinimized },

        initGroup(){
            if(this.config.refreshGroupOnLoad) this.$store.dispatch(this.gm_ns + '/refreshGroup', {group_id: this.group_id})
        },

        openConfig(){ this.showConfig = !this.showConfig },

        // if this group has less then N num of messages, store will trigger API for more messages
        getInitMessages(){ 
            this.$store.dispatch(this.gm_ns + '/getInitMessages').then(() => {
                this.$store.dispatch(`${this.gm_ns}/whoSawWhat`)
            })
        },

        listenForMessagesSeen(){
            Echo.private("group." + this.group.id)
            .listen('.message.seen', e => {
                this.$store.dispatch(this.gm_ns + '/seenEvent', e.data).then(() => {
                    this.$store.dispatch(this.gm_ns + '/whoSawWhat')
                })
            })
        },

        listenForParticipantRoleChange(){
            Echo.private("group." + this.group.id)
            .listen('.participant.role.change', e => {
                this.$store.dispatch(this.gm_ns + '/updateParticipantRoleEvent', e.data)
            })
        },

        listenForParticipantRemoved(){
            Echo.private("group." + this.group.id)
            .listen('.participant.removed', e => {
                this.$store.dispatch(this.gm_ns + '/removedParticipantEvent', e.data)
            })
        },

        listenForParticipantAdded(){
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
                    ? null // console.log("error, ChatWindow::listenForParticipantAdded() - user listens for event which he shouldn't be listening to since he was no longer participant in chat group.")  
                    : this.$store.dispatch(this.gm_ns + '/addedParticipantEvent', e.data)
            })
        },

        listenForUserLeftGroup(){
            Echo.private("group." + this.group.id)
            .listen('.participant.left', e => {
                this.$store.dispatch(this.gm_ns + '/participantLeftGroupEvent', e.data)
            })
        },

        listenForGroupNameChange(){
            Echo.private("group." + this.group.id)
            .listen('.group.new_name', e => {
                this.$store.dispatch(this.gm_ns + '/changeGroupNameEvent', e.data)
            })
        },

        createPermissions(){
            this.permissions = this.createObjectFromArrayValues(this.actionKeys)

            this.permission_canAdd()
            this.permission_canRemove()
            this.permission_canChangeRole()
            this.permission_canSendMessage()
            this.permission_canChangeGroupName()
        },

        permission_canAdd(){
            let action = 'add'
            this.ruleDepth3(this.rules[action][this.chatRole], action)
        },

        permission_canRemove(){
            let action = 'remove'
            this.ruleDepth3(this.rules[action][this.chatRole], action)
        },

        permission_canChangeRole(){
            let action = 'change_role'
            this.ruleDepth4(this.rules[action][this.chatRole], action)
        },


        permission_canSendMessage(){
            let action = 'send_message'
            this.ruleDepth2(this.rules[action][this.chatRole], action)
        },

        permission_canChangeGroupName(){
            let action = 'change_group_name'
            this.ruleDepth2(this.rules[action][this.chatRole], action)
        },

        ruleDepth2(level1, action){
            this.permissions[action] = level1[this.group.model_type] ? true : false
        },

        ruleDepth3(level1, action){
            for(let targetRole in level1){
                if(level1[targetRole][this.group.model_type]) this.permissions[action].push(targetRole)
            }
        },

        ruleDepth4(level1, action){
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

        createObjectFromArrayValues(array){
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
