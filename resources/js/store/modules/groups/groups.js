import * as helpers from "../../../helpers/helpers_exporter.js"

import { 
    getById, 
    getBySearchString, 
    getGroupIndexById, 
    getLatestMessageId, 
    getParticipantIndexInGroupByGroupId, 
    numOfUnseenGroups, 
    getGroupsFrom_openedGroupsIds, 
    getGroupsFrom_filteredGroupsIds,
    findGroupIndexByIdIn_openedGroupsIds,
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

    numOfUnseenGroups:(state) => numOfUnseenGroups(state.groups),

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
                context.dispatch('groups/getAllMessages', { group_id: group.id }, {root:true})
            })
            .catch((error) => {
                console.log('error trying to get group by id in (groups.js) which doesnt exist in store nor in database, openGroup action')
                console.log(error)
            })
        }

    },

    closeGroup({ commit, state }, id)
    {
        const index = findGroupIndexByIdIn_openedGroupsIds(state.openedGroupsIds, id)
        if(Number.isInteger(index)) commit('closeWindow', index)
    },

    addMessages(context, data)
    {
        const grI = getGroupIndexById(context.state.groups, data.group_id)
        if(grI == undefined){
            console.log('group with this index doesnt exist, also possible, group with this ID is not in store')
            return 0
        }

        context.commit('mergeMessagesInGroup', {
            grI: grI,
            messages: data.messages
        })

        const last_msg_id = getLatestMessageId(context.state.groups[grI].messages)

        context.commit('setLastMessageIdInGroup', {
            grI: grI,
            last_msg_id: last_msg_id
        })
        
        context.commit('updateGroup_updated_at', { 
            grI: grI,
            updated_at: new Date(Date.now()).toISOString()
         })
        
        // set has unseen state only if he is not owner of that message
        if(context.rootState.auth.user.id != context.state.groups[grI].messages[last_msg_id].user_id) {
            context.commit('hasUnseenState', { grI: grI })
        }
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

    getAllMessages(context, data)
    {
        axios.get('/api/chat/group/' + data.group_id + '/messages')
        .then(res => {
            const messages = Object.assign({}, helpers.createHashMap_OneToOne(res.data.messages, 'id') )

            if(Object.keys(messages).length) context.dispatch('groups/addMessages', {
                group_id: data.group_id,
                messages: messages,
                seen_states: res.data.seen_states
            }, {root:true})

        })
        .catch(error => {
            console.log('error in getting messages', error)
        });
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