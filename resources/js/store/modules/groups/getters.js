import * as h from './helper_functions.js'

import {
    sort_filteredGroups_by_updated_at
} from './sorters.js'

const getters = {
    allGroups: (state) => state.groups,

    getGroupById: (state) => (id) => state.groups[id],
    
    openedGroupsIds: (state) => state.openedGroupsIds,

    numOfUnseenGroups: (state) => h.getNumOfUnseenGroups(state.groups),

    filteredGroups: (state) => sort_filteredGroups_by_updated_at(h.getModelsFromIds(state.groups, state.filteredGroupsIds)),

    getUserRole: (state) => (data) => state.groups[data.group_id].participants[data.user_id].pivot.participant_role,

    getMyParticipants: (state) => (group_id) => state.groups[group_id].participants,

    getParticipant: (state) => (data) => state.groups[data.group_id].participants[data.participant_id],

    getOwnerIdOfLastMessage: (state) => (data) => state.groups[data.group_id].messages[data.last_msg_id].user_id,

    getGroupLastMsgId: (state) => (data) => state.groups[data.group_id].last_msg_id,

    getGroupLastMsg: (state) => (group_id) => state.groups[group_id].last_message ? state.groups[group_id].last_message : null,

    getPossesedNumberOfMessagesInGroup: (state) => (group_id) => Object.keys(state.groups[group_id].messages).length,

    getLastPossesedMsgId: (state) => (group_id) => h.getMinObjKey(state.groups[group_id].messages),
}

export default getters 