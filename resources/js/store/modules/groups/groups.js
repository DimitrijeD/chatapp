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
            var groups = m.attachUnseenStateBool(res.data, context.rootState.auth.user.id)
            groups = m.propInit(groups)

            context.commit('addGroups', groups)
            context.dispatch('filterGroupsBySearchString', '')
        })
    },

    storeGroup(context, data)
    {
        axios.post('/api/chat/group/store', data)
        .then( res => {
            var groups = [res.data]
            groups = m.propInit(groups)
            groups = m.attachUnseenStateBool(groups, context.rootState.auth.user.id)
            const group = groups[0]

            context.commit('addGroup', group)
            context.commit('addToOpenedGroups', group.id)
            context.commit('addToFilteredGroups', group.id)
        });
    },

    openGroup(context, id)
    {
        const group = h.getById(state.groups, id)

        if(group){
            if(!state.openedGroupsIds.includes(id)) 
                context.commit('addToOpenedGroups', group.id)  
        } else {
            axios.get('/api/chat/group/' + id).then((res)=>{
                let groups = [res.data]
                groups = m.propInit(groups)
                groups = m.attachUnseenStateBool(groups, context.rootState.auth.user.id)
                const group = groups[0]

                context.commit('addGroup', group)
                context.commit('addToOpenedGroups', group.id)
                context.commit('addToFilteredGroups', group.id)
            })
            .catch((error) => {
                console.log('groups/openGroup')
                console.log('Trying to get group by id which doesnt exist in store nor in database.')
                console.log(error)
            })
        }

    },

    closeGroup({ commit, state }, id)
    {
        const index = h.getGroupIndexByIdIn_openedGroupsIds(state.openedGroupsIds, id)
        if(Number.isInteger(index)) commit('closeWindow', index)
    },

    setAllMessagesAcknowledged({ commit, state }, data)
    {
        const grI = h.getGroupIndexById(state.groups, data.group_id)
        
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
        const grI = h.getGroupIndexById(state.groups, e.group_id)
        const prI = h.getParticipantIndexInGroupByGroupId(state.groups[grI].participants, e.user_id)

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
            const grI = h.getGroupIndexById(context.state.groups, res.data.group_id)
            const prI = h.getParticipantIndexInGroupByGroupId(context.state.groups[grI].participants, res.data.user_id)

            const data = {
                messages: message,
                grI: grI,
                prI: prI,
                lastAcknowledgedMessageId: res.data.id,
                last_msg_id: res.data.id,
                now: h.nowISO()
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
        request.grI = h.getGroupIndexById(state.groups, request.group_id)
        request.lastOwnedMsgId = h.getLastOwnedMsgId(state.groups[request.grI].messages)

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
            const prI = h.getParticipantIndexInGroupByGroupId(context.state.groups[request.grI].participants, res.data.user_id)
            // console.log('number of fetched messages', res.data.length)

            const data = {
                messages: messages,
                grI: request.grI,
                prI: prI,
                last_msg_id: h.getLastOwnedMsgId(res.data),
                now: h.nowISO()
            }

            context.commit('mergeMessagesInGroup', data)
            context.commit('setLastMessageIdInGroup', data) 
            context.dispatch('setEarliestMessageInGroup', data) 
        }).catch( error => {
            console.log('groups/getInitMessages')
            console.log(error)
        })
    },

    getOnlyMissingMessages(context, request)
    {
        if(!state.groups[request.grI].last_msg_id){
            console.log(`Trying to get latest missing messages in \n group_id = ${request.group_id} \n which doesnt have 'last_msg_id' set to integer value \n which is required for successful response`)
            return
        }
        const endpoint = `/api/chat/group/${request.group_id}/from-msg/${state.groups[request.grI].last_msg_id}`

        return axios.get(endpoint).then(res => {
            if(!res.data.length) return

            const data = {
                messages: helpers.createHashMap_OneToOne(res.data, 'id'),
                grI: request.grI,
                prI: h.getParticipantIndexInGroupByGroupId(context.state.groups[request.grI].participants, res.data.user_id),
                last_msg_id: h.getLastOwnedMsgId(res.data),
                now: h.nowISO()
            }

            context.commit('mergeMessagesInGroup', data)
            context.commit('setLastMessageIdInGroup', data) 
            context.commit('hasUnseenState', data) // after pulling new messages, user must have unseen messages, otherwise code is dog
            context.commit('updateGroup_updated_at', data)
            // context.dispatch('setEarliestMessageInGroup', data) 
            context.dispatch('updateOrderOfRecentGroups') 
        }).catch( error => {
            console.log('groups/getOnlyMissingMessages')
            console.log(error)
        })
    },

    getEarliestMessages(context, request)
    {
        const grI = h.getGroupIndexById(state.groups, request.group_id) 

        if(state.groups[grI].reachedEarliestMsgId){
            console.log('earliest message ID reached, cannot pull any more')
            return
        }

        const eariestMessageId = state.groups[grI].eariestMessageId

        if(!eariestMessageId){
            console.log(`trying to get earliest messages in \n group_id = ${request.group_id} \n which doesnt have 'eariestMessageId' set to integer value \n which is required for successful response`)
            return
        }

        const endpoint = `/api/chat/group/${request.group_id}/before-msg/${eariestMessageId}`

        return axios.get(endpoint).then(res => {
            if(!res.data.length) {
                console.log('no messages returned')
                context.commit('lockReachedEarliestMsgId', grI)
                return
            }

            const data = {
                messages: helpers.createHashMap_OneToOne(res.data, 'id'),
                grI: grI,
            }

            context.commit('mergeMessagesInGroup', data)
            context.dispatch('setEarliestMessageInGroup', data) 
        }).catch( error => {
            console.log('groups/getEarliestMessages')
            console.log(error)
        })
    },

    addParticipants(context, data)
    {
        let request = m.prepareParticipantsForStoreRequest(data)
        let endpoint = `/api/chat/group/${data.group_id}/add-users`

        /**
         * Request returns fresh group with particiapants, without messages
         */
        return axios.post(endpoint, request).then(res => {
            if(!res.data.group){ 
                console.log("added participants to group request but no fresh group was returned, 'groups/addParticipants' action")
                return
            }

            let group = res.data.group

            let data = {
                group: group,
                grI: h.getGroupIndexById(state.groups, group.id) 
            }
            context.commit('refreshGroup', data)

        }).catch( error => {
            console.log('groups/addParticipants')
            console.log(error)
        })
    },

    removeParticipantFromGroup(context, data)
    {
        if(!data.group_id || !data.user_id_to_remove) return

        axios.get(`/api/chat/group/${data.group_id}/remove/${data.user_id_to_remove}`).then((res)=>{
            const grI = h.getGroupIndexById(state.groups, data.group_id) 
            const prI = h.getParticipantIndexInGroupByGroupId(state.groups[grI].participants, data.user_id_to_remove)

            context.commit('removeParticipantFromGroup', {
                grI: grI,
                prI: prI
            })
        }).catch((error)=> {
            console.log(`error while removing participant from group \n ${error}`)
        })
    },

    leaveGroup(context, data)
    {
        axios.get(`/api/chat/group/${data.group_id}/leave`).then((res) => {
            context.dispatch('closeGroup', data.group_id) 
            context.commit('removeGroupFromStore', h.getGroupIndexById(state.groups, data.group_id) )
            context.commit('removeFilteredGroupsId', data.group_id)
            
        }).catch(error => {
            console.log(error)
        })
    },

    setEarliestMessageInGroup(context, data)
    {
        data.eariestMessageId = h.getEarliestMsgId(state.groups[data.grI].messages)
        context.commit('setEarliestMessageIdInGroup', data)
    },

    updateOrderOfRecentGroups(context)
    {
        // if first group in list is one triggering this method, there is no need to update 
        h.getAllIds(s.sort_filteredGroups_by_updated_at(h.getGroupsFrom_filteredGroupsIds(state.groups, state.filteredGroupsIds)))
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
        const grI = h.getGroupIndexById(state.groups, data.group_id) 
        const prI = h.getParticipantIndexInGroupByGroupId(state.groups[grI].participants, data.pivot.user_id)

        context.commit('patchParticipantRole', {
            grI: grI,
            prI: prI,
            participant_role: data.pivot.participant_role
        })
    }

}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
}