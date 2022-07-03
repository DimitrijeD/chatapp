import * as helpers from "../../../helpers/helpers_exporter.js"
import * as h from './helper_functions.js' // h as helper functions
import * as m from './modifiers.js'        // m as modifiers
import * as s from './sorters.js'          // s as sorters

import state from './state.js'
import getters from './getters.js'
import mutations from './mutations.js'
import axios from "axios"

const actions = 
{
    filterGroupsBySearchString: ({ commit, state }, str) => commit('setFilteredGroupsIds', h.getBySearchString(state.groups, str)),

    getGroups(context)
    {
        axios.get('/api/chat/user/groups').then((res)=>{
            var groups = helpers.createHashMap_OneToOne(res.data, 'id')

            for(let i in groups){
                groups[i].participants = helpers.createHashMap_OneToOne(groups[i].participants, 'id')
                context.dispatch('listenForNewMessages', {group_id: groups[i].id})
            }

            groups = m.attachUnseenStateBool(groups, context.rootState.auth.user.id)
            groups = m.propInit(groups)

            context.commit('addGroups', groups)
            context.dispatch('filterGroupsBySearchString', '')
        })
    },

    storeGroup(context, data)
    {
        axios.post('/api/chat/group/store', data)
        .then( res => {
            let group_id = res.data.id
            let groups = helpers.createHashMap_OneToOne([res.data], 'id')

            groups = m.propInit(groups)
            groups = m.attachUnseenStateBool(groups, context.rootState.auth.user.id)

            for(let i in groups){
                groups[i].participants = helpers.createHashMap_OneToOne(groups[i].participants, 'id')
            }

            context.dispatch('listenForNewMessages', {group_id: group_id})
            context.commit('addGroups', groups)
            context.commit('addToOpenedGroups', group_id)
            context.commit('addToFilteredGroups', group_id)
        })
    },

    openGroup(context, id)
    {
        const group = state.groups[id]

        if(group){
            if(!state.openedGroupsIds.includes(id)) 
                context.commit('addToOpenedGroups', group.id)  
        } else {
            context.dispatch('getMissingGroup', id)
        }
    },

    getMissingGroup(context, id)
    {
        axios.get('/api/chat/group/' + id).then((res)=>{
            let groups = helpers.createHashMap_OneToOne([res.data], 'id')
            let group_id = res.data.id

            groups = m.propInit(groups)
            groups = m.attachUnseenStateBool(groups, context.rootState.auth.user.id)

            for(let i in groups){
                groups[i].participants = helpers.createHashMap_OneToOne(groups[i].participants, 'id')
            }
            
            context.commit('addGroups', groups)
            context.commit('addToOpenedGroups', group_id)
            context.commit('addToFilteredGroups', group_id)
        })
        .catch((error) => {
            console.log('groups/getMissingGroup')
            console.log('Trying to get group by id which doesnt exist in store nor in database.')
            console.log(error)
        })
    },

    closeGroup({ commit, state }, group_id)
    {
        const index = state.openedGroupsIds.indexOf(group_id)

        if(index > -1) { 
            commit('closeWindow', index)
        }
    },

    setAllMessagesAcknowledged({ commit, state }, data)
    {
        axios.post('/api/chat/message/seen', {
            'group_id': data.group_id,
            'lastMessageId': data.lastAcknowledgedMessageId,
        }).then(res => {
            commit('setAcknowledgedMessages', {
                group_id: data.group_id,
                lastAcknowledgedMessageId: data.lastAcknowledgedMessageId
            })
            commit('setSeenState', { 
                group_id: data.group_id, 
                seenState: false
            })
        })
    },

    seenEvent({ commit, state }, e)
    {
        if(!e.user_id) return 

        commit('updateSeenPivot', {
            pivot: e,
            group_id: e.group_id,
            participant_id: e.user_id
        })
    },

    storeMessage(context, message)
    {
        return axios.post('/api/chat/message/store', message)
        .then(res => {
            const data = {
                group_id: res.data.group_id,
                participant_id: res.data.user_id, 
                lastAcknowledgedMessageId: res.data.id, 
                last_msg_id: res.data.id, 
                now: h.nowISO(),
                seenState: false, 
            }

            context.commit('setAcknowledgedMessages', data) 
            context.commit('setLastMessageIdInGroup', data) 
            context.commit('updateSeenPivotOnly_last_message_seen_id', data) 
            context.commit('setSeenState', data)

        }).catch( error => {
            console.log('groups/storeMessage')
            console.log(error)
        })
    },

    getMessages(context, request)
    {
        request.lastOwnedMsgId = h.getLastOwnedMsgId(state.groups[request.group_id].messages)

        if(isNaN(request.lastOwnedMsgId)) {
            console.log('is Nan, check this out - 8392839')
            return
        }

        request.numMessages = context.getters.getPossesedNumberOfMessagesInGroup(request.group_id)

        if(request.numMessages == 0){
            context.dispatch('getInitMessages', request)
            return 
        } else {
            context.dispatch('getEarliestMessages', request)
        }
    },

    getInitMessages(context, request)
    {
        const endpoint = `/api/chat/group/${request.group_id}/latest-messages` 

        return axios.get(endpoint).then(res => {
            if(!res.data.length) return

            const messages = helpers.createHashMap_OneToOne(res.data, 'id')

            const data = {
                messages: messages,
                group_id: request.group_id,
                participant_id: res.data.user_id,
                last_msg_id: h.getLastOwnedMsgId(res.data),
                now: h.nowISO()
            }

            context.commit('mergeMessagesInGroup', data)
            context.commit('setLastMessageIdInGroup', data) 
        }).catch( error => {
            console.log('groups/getInitMessages')
            console.log(error)
        })
    },

    getOnlyMissingMessages(context, request)
    {
        if(!state.groups[request.group_id].last_msg_id){
            // console.log(`Trying to get latest missing messages in \n group_id = ${request.group_id} \n which doesnt have 'last_msg_id' set to integer value \n which is required for successful response`)
            return
        }
        const endpoint = `/api/chat/group/${request.group_id}/from-msg/${state.groups[request.group_id].last_msg_id}`

        return axios.get(endpoint).then(res => {
            if(!res.data.length) return

            const data = {
                messages: helpers.createHashMap_OneToOne(res.data, 'id'),
                group_id: request.group_id,
                participant_id: res.data.user_id,
                last_msg_id: h.getLastOwnedMsgId(res.data),
                now: h.nowISO()
            }

            context.commit('mergeMessagesInGroup', data)
            context.commit('setLastMessageIdInGroup', data) 
            context.commit('updateGroup_updated_at', data)
            context.dispatch('updateOrderOfRecentGroups') 
        }).catch( error => {
            console.log('groups/getOnlyMissingMessages')
            console.log(error)
        })
    },

    getEarliestMessages(context, request)
    {
        if(state.groups[request.group_id].reachedEarliestMsgId){
            console.log('earliest message ID reached, cannot pull any more')
            return
        }

        const eariestMessageId = context.getters.getLastPossesedMsgId(request.group_id)
        
        const endpoint = `/api/chat/group/${request.group_id}/before-msg/${eariestMessageId}`

        return axios.get(endpoint).then(res => {
            if(!res.data.length) {
                // console.log('no messages returned')
                context.commit('lockReachedEarliestMsgId', request.group_id)
                return
            }

            const data = {
                messages: helpers.createHashMap_OneToOne(res.data, 'id'),
                group_id: request.group_id
            }

            context.commit('mergeMessagesInGroup', data)
        }).catch( error => {
            console.log('groups/getEarliestMessages')
            console.log(error)
        })
    },

    addParticipants(context, data)
    {
        data = m.prepareParticipantsForStoreRequest(data)

        return axios.post(`/api/chat/group/${data.group_id}/add-users`, data).then(res => {
            
        }).catch( error => {
            console.log('groups/addParticipants')
            console.log(error)
        })
    },

    removeParticipantFromGroup(context, data)
    {
        if(!data.group_id || !data.user_id_to_remove) return

        axios.get(`/api/chat/group/${data.group_id}/remove/${data.user_id_to_remove}`).then((res)=>{
            // display message from repsonse
        }).catch((error)=> {
            console.log(`error while removing participant from group \n ${error}`)
        })
    },

    leaveGroup(context, data)
    {
        axios.get(`/api/chat/group/${data.group_id}/leave`).then((res) => {
            context.dispatch('clearGroupData', data) 
        }).catch(error => {
            console.log(error)
        })
    },

    clearGroupData(context, data)
    {
        context.dispatch('closeGroup', data.group_id) 
        context.commit('removeGroupFromStore', data.group_id) 
        context.commit('removeFilteredGroupsId', data.group_id)

        Echo.leave('group.' + data.group_id)
    },

    updateOrderOfRecentGroups(context)
    {
        context.commit('setFilteredGroupsIds', h.getAllIds(s.sort_filteredGroups_by_updated_at(h.getModelsFromIds(state.groups, state.filteredGroupsIds))))
    },

    changeParticipantRole(context, data)
    {
        axios.post("/api/chat/group/change-user-role", data)
        .then(res => {
            // console.log(res.data) success message here
        })
    },

    patchParticipantRole(context, data)
    {
        context.commit('patchParticipantRole', {
            group_id: data.group_id,
            participant_id: data.pivot.user_id,
            participant_role: data.pivot.participant_role
        })
    },

    removedParticipantEvent(context, data)
    {
        context.commit('removeParticipantFromGroup', {
            group_id: data.group_id,
            participant_id: data.removed_user_id,
        })
    },

    addedParticipantEvent(context, data)
    {
        if(!state.groups[data.group_id]) context.dispatch('getMissingGroup', data.group_id)
        
        context.commit('refreshGroupParticipants', {
            group_id: data.group_id,
            participants: helpers.createHashMap_OneToOne(data.participants, 'id')
        })
    },

    participantLeftGroupEvent(context, data)
    {
        context.commit('removeParticipantFromGroup', {
            group_id: data.group_id,
            participant_id: data.user_left_id,
        })
    },

    changeGroupName(context, data)
    {
        axios.post("/api/chat/group/change-group-name", data)
        .then(res => {
            // console.log(res.data) success message here
        })
    },

    changeGroupNameEvent(context, data)
    {
        context.commit('changeGroupName', {
            group_id: data.group_id,
            new_name: data.new_name
        })
    },

    newMessageEvent(context, data)
    {
        context.commit('setLastMessage', {
            group_id: data.group_id, 
            last_message: data 
        })

        context.commit('mergeMessagesInGroup', {
            group_id: data.group_id, 
            messages: { [data.id]: data } 
        })

        // If received message is not mine, then thats a message I haven't seen yet
        if(context.rootState.auth.user.id != data.user_id){
            context.commit('setSeenState', {
                group_id: data.group_id, 
                seenState: true
            })
        }
        context.commit('updateGroup_updated_at', {
            group_id: data.group_id,
            now: h.nowISO()
        })
        context.dispatch('updateOrderOfRecentGroups')
    },

    listenForNewMessages(context, data)
    {
        Echo.private("group." + data.group_id)
        .listen('.message.new', e => {
            context.dispatch('newMessageEvent', e.data)
        })
    },

    refreshGroup(context, data)
    {
        axios.get(`/api/chat/group/refresh/${data.group_id}`).then(res => {

            context.commit('refreshGroupParticipants', {
                group_id: data.group_id,
                participants: helpers.createHashMap_OneToOne(res.data.participants, 'id')
            })

            context.commit('mergeMessagesInGroup', {
                messages: helpers.createHashMap_OneToOne(res.data.latest_messages, 'id'),
                group_id: res.data.id,
            })

            context.dispatch('updateOrderOfRecentGroups') 
        })
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
}