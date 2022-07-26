import Vue from 'vue'
import defaultState_groups from './defaultState_groups.js'

const mutations = {
    setFilteredGroupsIds: (state, ids) => state.filteredGroupsIds = ids,
    addToFilteredGroups: (state, id) => state.filteredGroupsIds.push(id),
    removeFilteredGroupsId: (state, id) => state.filteredGroupsIds = state.filteredGroupsIds.filter((item) => item !== id ),

    addGroupsIds: (state, groupsIds) => state.groupsIds = state.groupsIds.concat(groupsIds),

    removeGroupId: (state, group_id) => state.groupsIds.splice(state.groupsIds.indexOf(group_id), 1),

    openWindow: (state, group_id) => state.openedGroupsIds.push(group_id),

    closeWindow: (state, group_id) => {
        const index = state.openedGroupsIds.indexOf(group_id)
        if(index > -1){
            state.openedGroupsIds.splice(index, 1)
        } else {
            console.log(`Group with id of ${group_id} is not in store.groups.openedGroupsIds`)
        }
    },

    numGroupsWithUnseen: (state, int) => state.numGroupsWithUnseen = int,

    resetState: (state) => Object.assign(state, defaultState_groups()),
}

export default mutations 