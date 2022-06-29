const mutations = {
    addGroups: (state, groups) => state.groups = groups,

    refreshGroup(state, data){ 
        state.groups[data.grI].updated_at = data.group.updated_at
        state.groups[data.grI].last_msg_id = data.group.last_msg_id
        state.groups[data.grI].participants = data.group.participants
    },

    setFilteredGroupsIds: (state, ids) => state.filteredGroupsIds = ids,

    removeFilteredGroupsId: (state, id) => state.filteredGroupsIds = state.filteredGroupsIds.filter((item) => item !== id ),

    addGroup: (state, group) => state.groups.push(group),

    addToOpenedGroups: (state, id) => state.openedGroupsIds.push(id),

    closeWindow: (state, index) => state.openedGroupsIds.splice(index, 1),

    mergeMessagesInGroup: (state, data) => state.groups[data.grI].messages = { ...state.groups[data.grI].messages, ...data.messages},

    setAcknowledgedMessages: (state, data) => state.groups[data.grI].lastAcknowledgedMessageId = data.lastAcknowledgedMessageId,

    setLastMessageIdInGroup: (state, data) => state.groups[data.grI].last_msg_id = data.last_msg_id,

    setEarliestMessageIdInGroup: (state, data) => state.groups[data.grI].eariestMessageId = data.eariestMessageId,

    doesntUnseenState: (state, data) => state.groups[data.grI].hasUnseenState = false,

    hasUnseenState: (state, data) => state.groups[data.grI].hasUnseenState = true,

    updateSeenPivot: (state, data) => state.groups[data.grI].participants[data.prI].pivot = data.pivot,

    updateSeenPivotOnly_last_message_seen_id(state, data) {
        state.groups[data.grI].participants[data.prI].pivot.last_message_seen_id = data.last_msg_id
        state.groups[data.grI].participants[data.prI].pivot.updated_at = data.now
    },

    addToFilteredGroups: (state, id) => state.filteredGroupsIds.push(id),

    updateGroup_updated_at: (state, data) => state.groups[data.grI].updated_at = data.now,

    removeGroupFromStore: (state, index) => state.groups.splice(index, 1),

    removeParticipantFromGroup: (state, data) => state.groups[data.grI].participants.splice(data.prI, 1),

    // once this is set, earliest messages request can no longer be trigged (up to 'first' message in group is pulled)
    lockReachedEarliestMsgId: (state, grI) => state.groups[grI].reachedEarliestMsgId = true, 

    patchParticipantRole: (state, data) => state.groups[data.grI].participants[data.prI].pivot.participant_role = data.participant_role,

    refreshGroupParticipants: (state, data) => state.groups[data.grI].participants = data.participants,

    changeGroupName: (state, data) => state.groups[data.grI].name = data.new_name,
}

export default mutations 