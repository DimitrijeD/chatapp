export default function defaultState_groups(){ 
    return {
        groupsIds: [], // all id-s of groups user got from API
        openedGroupsIds: [], // all id-s of groups user opened
        filteredGroupsIds: [], // all calculated id-s of groups that match search params
        numGroupsWithUnseen: 0,
    }
}