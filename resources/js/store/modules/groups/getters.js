import * as helpers from './helper_functions.js'

import {
    sort_filteredGroups_by_updated_at
} from './sorters.js'

const getters = {
    allGroups: (state) => state.groups,

    filterById: (state) => (id) => helpers.getById(state.groups, id),
    
    openedGroupsIds: (state) => state.openedGroupsIds,

    numOfUnseenGroups: (state) => helpers.getNumOfUnseenGroups(state.groups),

    filteredGroups: (state) => { 
        return sort_filteredGroups_by_updated_at(helpers.getGroupsFrom_filteredGroupsIds(state.groups, state.filteredGroupsIds))
    },

    getUserRole: (state) => (data) =>
    {
        const grI = helpers.getGroupIndexById(state.groups, data.group_id)
        const prI = helpers.getParticipantIndexInGroupByGroupId(state.groups[grI].participants, data.user_id)
        return state.groups[grI].participants[prI].pivot.participant_role
    },

    getMyParticipants: (state) => (group_id) => state.groups[helpers.getGroupIndexById(state.groups, group_id)].participants,
}

export default getters 