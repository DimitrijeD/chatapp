import * as helpers from './helper_functions.js'

import {
    sort_filteredGroups_by_updated_at
} from './sorters.js'

const getters = {
    allGroups: (state) => state.groups,

    getGroupById: (state) => (id) => state.groups[id],
    
    openedGroupsIds: (state) => state.openedGroupsIds,

    numOfUnseenGroups: (state) => helpers.getNumOfUnseenGroups(state.groups),

    filteredGroups: (state) => { 
        return sort_filteredGroups_by_updated_at(helpers.getGroupsFrom_filteredGroupsIds(state.groups, state.filteredGroupsIds))
    },

    getUserRole: (state) => (data) =>
    {
        return state.groups[data.group_id].participants[data.user_id].pivot.participant_role
    },

    getMyParticipants: (state) => (group_id) => state.groups[group_id].participants,

    getParticipant: (state) => (data) =>
    {
        return state.groups[data.group_id].participants[data.participant_id]
    },

    getOwnerIdOfLastMessage: (state) => (data) => 
    {
        return state.groups[data.group_id].messages[data.last_msg_id].user_id
    },

    getGroupLastMsgId: (state) => (data) => state.groups[data.group_id].last_msg_id,
}

export default getters 