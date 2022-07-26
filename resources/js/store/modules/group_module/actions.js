import * as h from '../../functions/helpers.js'
import * as ns from '../../module_namespaces'
import store from '../../index'
import { contextMap } from 'tailwindcss/lib/jit/lib/sharedState.js'

const actions = 
{
    initGroup({ state, rootState, commit, dispatch, getters, rootGetters }, group)
    {
        const selfId = rootState.auth.user.id
        if(group?.id)           commit('id',           group.id)
        if(group?.last_message) commit('last_message', group.last_message)
        if(group?.messages)     commit('addMessages',  group.messages)
        if(group?.model_type)   commit('model_type',   group.model_type)
        if(group?.name)         commit('name',         group.name)
        if(group?.created_at)   commit('created_at',   group.created_at)
        if(group?.updated_at)   commit('updated_at',   group.updated_at)

        if(group?.participants){
            commit('addParticipants', group.participants)
            commit('seen', h.evalSeenState(
                h.getById(group.participants, selfId), 
                group?.last_message
            ))
        }

    },

    allMessagesSeen({ state, commit }, lastMessageId)
    {
        axios.post('/api/chat/message/seen', {
            'group_id': state.id,
            'lastMessageId': lastMessageId,
        }).then(res => {
            // res. data onlyt success message
            commit('seen', true)
        })
    },

    storeMessage({ commit, dispatch }, message)
    {
        return axios.post('/api/chat/message/store', message).then(res => {
            commit('updatePivot', {
                last_message_seen_id: res.data.id,
                participant_id: res.data.user_id, 
                now: h.nowISO(),
            }) 
            commit('seen', true)
            dispatch(ns.groupsManager() + '/numGroupsWithUnseen', null, {root:true})
        }).catch( error => { console.log('gv1/storeMessage', error) })
    },

    getInitMessages({ state, commit, dispatch, rootState })
    {
        return axios.get(`/api/chat/group/${state.id}/latest-messages`).then(res => {
            commit('addMessages',  res.data)

            dispatch('shouldLockEarliestMsges', {
                response_length: res.data.length,
                rule_length: rootState[ns.chat_rules()].init_num_messages
            })

        }).catch( error => { console.log(`${ns.groupModule(state.id)}/getInitMessages`, error) })
    },

    getEarliestMessages({ state, commit, getters, dispatch, rootState })
    {
        if(state.messages_tracker.gotEarliestMsg) return // All old messages are in store

        return axios.get(`/api/chat/group/${state.id}/before-msg/${getters.getLastPossesedMsgId}`).then(res => {
            commit('addMessages', res.data)

            dispatch('shouldLockEarliestMsges', {
                response_length: res.data.length,
                rule_length: rootState[ns.chat_rules()].earliest_num_messages
            })

        }).catch( error => { console.log(`${ns.groupModule(state.id)}/getEarliestMessages`, error) })
    },

    shouldLockEarliestMsges({ commit }, data)
    {
        if(data.response_length < data.rule_length) commit('gotEarliestMsg', true)
    },

    addParticipants({ state }, data)
    {
        data.group_id = state.id
        
        return axios.post(`/api/chat/group/${state.id}/add-users`, h.prepareParticipantsForStoreRequest(data)).then(res => {
            //
        }).catch( error => { console.log(`${ns.groupModule(state.id)}/addParticipants`, error) })
    },

    removeParticipant({ state }, participant_id)
    {
        axios.get(`/api/chat/group/${state.id}/remove/${participant_id}`).then((res)=>{
            // display message from repsonse
        }).catch((error)=> { console.log(`error while removing participant from group \n ${error}`) })
    },

    leaveGroup({ state, dispatch })
    {
        axios.get(`/api/chat/group/${state.id}/leave`).then((res) => {
            dispatch('purgeGroup') 
        }).catch(error => { console.log(error) })
    },

    changeParticipantRole({ state }, data)
    {
        data.group_id = state.id
        
        axios.post("/api/chat/group/change-user-role", data).then(res => {
            // console.log(res.data) success message here
        })
    },

    seenEvent({ rootState, commit, dispatch }, data)
    {
        commit('updatePivot', {
            last_message_seen_id: data.last_message_seen_id,
            participant_id: data.user_id,
            now: h.nowISO()
        })

        // this event is triggered by this.user,  meaning he is the one who saw last message
        if(rootState.auth.user.id == data.user_id) commit('seen', true)

        dispatch(ns.groupsManager() + '/numGroupsWithUnseen', null, {root:true})

    },

    updateParticipantRoleEvent({ commit }, data)
    {
        commit('updateParticipantRole', {
            participant_id: data.pivot.user_id,
            participant_role: data.pivot.participant_role
        })
    },

    removedParticipantEvent({ rootState, commit, dispatch }, data)
    {
        if(rootState.auth.user.id == data.removed_user_id){
            dispatch('purgeGroup').then(() => {
                // here is last line of purging group so i can
                // show some kind of message to user that he is no longer participant in that group
            })
        } else {
            commit('deleteParticipant', data.removed_user_id)
        }
    },

    addedParticipantEvent({ commit }, data)
    {
        commit('addParticipants', data.participants)
    },

    participantLeftGroupEvent({ commit }, data)
    {
        commit('deleteParticipant', data.user_left_id)
    },

    changeGroupName({ state }, data)
    {
        axios.post("/api/chat/group/change-group-name", data).then(res => {
            // TODO  console.log(res.data) success message here
        })
    },

    changeGroupNameEvent({ commit }, data)
    {
        commit('name', data.new_name)
    },

    newMessageEvent({ rootState, commit, dispatch }, msg)
    {
        commit('last_message', msg)
        commit('addMessages', [msg])
        commit('updated_at', h.nowISO())

        const selfId = rootState.auth.user.id

        // if message is "mine" set seen to true, 
        // else set to false
        commit('seen', selfId == msg.user_id)

        commit('updatePivot', {
            last_message_seen_id: msg.id,
            participant_id: msg.user_id,
            now: h.nowISO()
        })

        dispatch(ns.groupsManager() + '/numGroupsWithUnseen', null, {root:true})
        dispatch(ns.groupsManager() + '/sortNewstGroups',     null, {root:true})
    },

    listenForNewMessages({ dispatch }, group_id)
    {
        Echo.private("group." + group_id)
        .listen('.message.new', e => {
            dispatch('newMessageEvent', e.data).then(()=>{
                dispatch('whoSawWhat')
            })
        })
    },

    // refreshGroup({ state, commit, dispatch }, data)
    // {
    //     axios.get(`/api/chat/group/refresh/${state.id}`).then(res => {
    //         // commit('refreshGroupParticipants', res.data.participants)
    //         commit('addMessages', res.data.latest_messages)
    //         dispatch(ns.groupsManager() + '/sortNewstGroups', null, {root:true})
    //     })
    // },

    purgeGroup({ state, dispatch })
    {
        const group_id = state.id 
        dispatch(ns.groupsManager() + '/removeFilteredGroupsId', group_id, {root:true})
        
        dispatch(ns.groupsManager() + '/closeGroup', group_id, {root:true}).then(()=>{
            dispatch(ns.groupsManager() + '/removeGroupId', group_id, {root:true}).then(()=> {
                Echo.leave('group.' + group_id)
                store.unregisterModule(ns.groupModule(group_id))
            })
        })
        
    },

    /**
     * When group is instantiated
     * When new messages is received
     * When some1 sees message
     */
    whoSawWhat({ state, rootState, commit, dispatch, getters, rootGetters })
    {
        let whoSawWhat = {} 
        let last_message_seen_id = null
        for(let i in state.participants){
            last_message_seen_id = state.participants[i].pivot.last_message_seen_id
        
            if(last_message_seen_id){ 
                if(!whoSawWhat.hasOwnProperty(last_message_seen_id)) whoSawWhat[last_message_seen_id] = []

                whoSawWhat[last_message_seen_id].push(state.participants[i].id)
            } 
        }

        commit('whoSawWhat', whoSawWhat)
    },
    
}

export default actions