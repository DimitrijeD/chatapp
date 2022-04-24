import * as helpers from "../../../helpers/helpers_exporter.js"

import { 
    getById, 
    getBySearchString, 
    getGroupIndexById, 
    getLastOwnedMsgId, 
    getParticipantIndexInGroupByGroupId, 
    getNumOfUnseenGroups, 
    getGroupsFrom_openedGroupsIds, 
    getGroupsFrom_filteredGroupsIds,
    getGroupIndexByIdIn_openedGroupsIds,
    nowISO,
} from './getters.js'

import {
    sort_filteredGroups_by_updated_at
} from './sorters.js'

import { 
    attachUnseenStateBool, 
    propInit,
} from './modifiers.js'

//----------------------------------------------------------------------------------------------------

const state = {
    groups: [], 
    openedGroupsIds: [],
    filteredGroupsIds: [],
}

const getters = {
    allGroups: (state) => state.groups,

    filterById: (state) => (id) => getById(state.groups, id),
    
    openedGroupsIds: (state) => state.openedGroupsIds,

    numOfUnseenGroups:(state) => getNumOfUnseenGroups(state.groups),

    getOpenedGroups: (state) => getGroupsFrom_openedGroupsIds(state.groups, state.openedGroupsIds),

    filteredGroups: (state) => sort_filteredGroups_by_updated_at(getGroupsFrom_filteredGroupsIds(state.groups, state.filteredGroupsIds)),

}

const actions = 
{
    filterGroupsBySearchString: ({ commit, state }, str) => commit('setFilteredGroupsIds', getBySearchString(state.groups, str)),

    getGroups(context)
    {
        axios.get('/api/chat/user/groups').then((res)=>{
            var groups = attachUnseenStateBool(res.data, context.rootState.auth.user.id)
            groups = propInit(groups)

            context.commit('addGroups', groups)
            context.dispatch('filterGroupsBySearchString', '')
        })
    },

    storeGroup(context, data)
    {
        axios.post('/api/chat/group/store', data)
        .then( res => {
            var groups = [res.data]
            groups = propInit(groups)
            groups = attachUnseenStateBool(groups, context.rootState.auth.user.id)
            const group = groups[0]

            context.commit('addGroup', group)
            context.commit('addToOpenedGroups', group.id)
            context.commit('addToFilteredGroups', group.id)
        });
    },

    openGroup(context, id)
    {
        const group = getById(state.groups, id)

        if(group){
            if(!state.openedGroupsIds.includes(id)) context.commit('addToOpenedGroups', group.id)  
        } else {
            axios.get('/api/chat/group/' + id).then((res)=>{
                let groups = [res.data]
                groups = propInit(groups)
                groups = attachUnseenStateBool(groups, context.rootState.auth.user.id)
                const group = groups[0]

                context.commit('addGroup', group)
                context.commit('addToOpenedGroups', group.id)
                context.commit('addToFilteredGroups', group.id)
            })
            .catch((error) => {
                console.log('error trying to get group by id in (groups.js) which doesnt exist in store nor in database, openGroup action')
                console.log(error)
            })
        }

    },

    closeGroup({ commit, state }, id)
    {
        const index = getGroupIndexByIdIn_openedGroupsIds(state.openedGroupsIds, id)
        if(Number.isInteger(index)) commit('closeWindow', index)
    },

    setAllMessagesAcknowledged({ commit, state }, data)
    {
        const grI = getGroupIndexById(state.groups, data.group_id)
        
        if(grI == undefined){
            console.log('group with this index doesnt exist, also possible, group with this ID is not in store')
            return 0
        }

        axios.post('/api/chat/message/seen', {
            'group_id': data.group_id,
            'lastMessageId': data.lastAcknowledgedMessageId,
        }).then(res => {
            commit('setAcknowledgedMessages', {
                grI: grI,
                lastAcknowledgedMessageId: data.lastAcknowledgedMessageId
            })
            commit('doesntUnseenState', { grI: grI })
        })
    },

    seenEvent({ commit, state }, e)
    {
        const grI = getGroupIndexById(state.groups, e.group_id)
        const prI = getParticipantIndexInGroupByGroupId(state.groups[grI].participants, e.user_id)

        if(!prI) return 

        commit('updateSeenPivot', {
            pivot: e,
            grI: grI,
            prI: prI
        })
    },

    storeMessage(context, message)
    {
        return axios.post('/api/chat/message/store', message)
        .then(res => {
            const message = helpers.createHashMap_OneToOne([res.data], 'id')
            const grI = getGroupIndexById(context.state.groups, res.data.group_id)
            const prI = getParticipantIndexInGroupByGroupId(context.state.groups[grI].participants, res.data.user_id)

            const data = {
                messages: message,
                grI: grI,
                prI: prI,
                lastAcknowledgedMessageId: res.data.id,
                last_msg_id: res.data.id,
                now: nowISO()
            }

            context.commit('mergeMessagesInGroup', data)
            context.commit('setAcknowledgedMessages', data) 
            context.commit('setLastMessageIdInGroup', data) 
            context.commit('updateSeenPivotOnly_last_message_seen_id', data) 
            context.commit('doesntUnseenState', data)
        }).catch( error => {
            console.log('groups/storeMessage')
            console.log(error)
        })
    },

    getMessages(context, request)
    {
        request.grI = getGroupIndexById(state.groups, request.group_id)
        request.lastOwnedMsgId = getLastOwnedMsgId(state.groups[request.grI].messages)

        if(isNaN(request.lastOwnedMsgId)) return

        if(request.lastOwnedMsgId == 0){
            context.dispatch('getInitMessages', request)

        } else {
            context.dispatch('getOnlyMissingMessages', request)
        }
    },

    getInitMessages(context, request)
    {
        const endpoint = `/api/chat/group/${request.group_id}/latest-messages` 

        return axios.get(endpoint).then(res => {
            if(!res.data.length) return

            const messages = helpers.createHashMap_OneToOne(res.data, 'id')
            const prI = getParticipantIndexInGroupByGroupId(context.state.groups[request.grI].participants, res.data.user_id)
            // console.log('number of fetched messages', res.data.length)

            const data = {
                messages: messages,
                grI: request.grI,
                prI: prI,
                last_msg_id: getLastOwnedMsgId(res.data),
                now: nowISO()
            }

            context.commit('mergeMessagesInGroup', data)
            context.commit('setLastMessageIdInGroup', data) 
        }).catch( error => {
            console.log('groups/storeMessage')
            console.log(error)
        })
    },

    getOnlyMissingMessages(context, request)
    {
        const endpoint = `/api/chat/group/${request.group_id}/from-msg/${state.groups[request.grI].last_msg_id}`

        return axios.get(endpoint).then(res => {
            if(!res.data.length) return

            const messages = helpers.createHashMap_OneToOne(res.data, 'id')
            const prI = getParticipantIndexInGroupByGroupId(context.state.groups[request.grI].participants, res.data.user_id)

            const data = {
                messages: messages,
                grI: request.grI,
                prI: prI,
                last_msg_id: getLastOwnedMsgId(res.data),
                now: nowISO()
            }

            context.commit('mergeMessagesInGroup', data)
            context.commit('setLastMessageIdInGroup', data) 
            context.commit('hasUnseenState', data) // after pulling new messages, user must have unseen messages, otherwise code is dog
        }).catch( error => {
            console.log('groups/storeMessage')
            console.log(error)
        })
    },

}

const mutations = {
    addGroups: (state, groups) => state.groups = groups,

    setFilteredGroupsIds: (state, ids) => state.filteredGroupsIds = ids,

    addGroup: (state, group) => state.groups.push(group),

    addToOpenedGroups: (state, id) => state.openedGroupsIds.push(id),

    closeWindow: (state, index) => state.openedGroupsIds.splice(index, 1),

    mergeMessagesInGroup: (state, data) => state.groups[data.grI].messages = { ...state.groups[data.grI].messages, ...data.messages},

    setAcknowledgedMessages: (state, data) => state.groups[data.grI].lastAcknowledgedMessageId = data.lastAcknowledgedMessageId,

    setLastMessageIdInGroup: (state, data) => state.groups[data.grI].last_msg_id = data.last_msg_id,

    doesntUnseenState: (state, data) => state.groups[data.grI].hasUnseenState = false,

    hasUnseenState: (state, data) => state.groups[data.grI].hasUnseenState = true,

    updateSeenPivot: (state, data) => state.groups[data.grI].participants[data.prI].pivot = data.pivot,

    updateSeenPivotOnly_last_message_seen_id(state, data) {
        state.groups[data.grI].participants[data.prI].pivot.last_message_seen_id = data.last_msg_id
        state.groups[data.grI].participants[data.prI].pivot.updated_at = data.now
    },

    addToFilteredGroups: (state, id) => state.filteredGroupsIds.push(id),

    updateGroup_updated_at: (state, data) => state.groups[data.grI].updated_at = data.updated_at,
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
}