import Vue from 'vue'

const mutations = {
    refreshGroup(state, data){ 
        state.groups[data.group_id].updated_at = data.group.updated_at
        state.groups[data.group_id].last_msg_id = data.group.last_msg_id
        state.groups[data.group_id].participants = data.group.participants
    },

    setFilteredGroupsIds: (state, ids) => state.filteredGroupsIds = ids,

    removeFilteredGroupsId: (state, id) => state.filteredGroupsIds = state.filteredGroupsIds.filter((item) => item !== id ),

    addGroups: (state, group) => state.groups = {...state.groups,  ...group },

    addToOpenedGroups: (state, id) => state.openedGroupsIds.push(id),

    closeWindow: (state, index) => state.openedGroupsIds.splice(index, 1),

    mergeMessagesInGroup: (state, data) => state.groups[data.group_id].messages = { ...state.groups[data.group_id].messages, ...data.messages},

    setAcknowledgedMessages: (state, data) => state.groups[data.group_id].lastAcknowledgedMessageId = data.lastAcknowledgedMessageId,

    setLastMessageIdInGroup: (state, data) => state.groups[data.group_id].last_msg_id = data.last_msg_id,

    setEarliestMessageIdInGroup: (state, data) => state.groups[data.group_id].eariestMessageId = data.eariestMessageId,

    doesntUnseenState: (state, data) => state.groups[data.group_id].hasUnseenState = false,

    hasUnseenState: (state, data) => state.groups[data.group_id].hasUnseenState = true,

    updateSeenPivot: (state, data) => state.groups[data.group_id].participants[data.participant_id].pivot = data.pivot,

    updateSeenPivotOnly_last_message_seen_id(state, data) {
        state.groups[data.group_id].participants[data.participant_id].pivot.last_message_seen_id = data.last_msg_id
        state.groups[data.group_id].participants[data.participant_id].pivot.updated_at = data.now
    },

    addToFilteredGroups: (state, id) => state.filteredGroupsIds.push(id),

    updateGroup_updated_at: (state, data) => state.groups[data.group_id].updated_at = data.now,

    removeGroupFromStore: (state, group_id) => delete state.groups[group_id],

    removeParticipantFromGroup(state, data) {
        // delete state.groups[data.group_id].participants[data.participant_id], // didnt work

        // solution which made component with computed prop for this getter be reactive after deleting 
        Vue.delete(state.groups[data.group_id].participants, data.participant_id)
    }, 

    // once this is set, earliest messages request can no longer be trigged (up to 'first' message in group is pulled)
    lockReachedEarliestMsgId: (state, group_id) => state.groups[group_id].reachedEarliestMsgId = true, 

    patchParticipantRole: (state, data) => state.groups[data.group_id].participants[data.participant_id].pivot.participant_role = data.participant_role,

    refreshGroupParticipants: (state, data) => state.groups[data.group_id].participants = {...state.groups[data.group_id].participants, ...data.participants},

    changeGroupName: (state, data) => state.groups[data.group_id].name = data.new_name,
}

export default mutations 