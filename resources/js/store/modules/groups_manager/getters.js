import * as h from '../../functions/helpers.js'
import * as ns from '../../module_namespaces'
import store from '../../index' 

const getters = {
    groupsIds: (state) => state.groupsIds,

    openedGroupsIds: (state) => state.openedGroupsIds,

    filteredGroupsIds: (state) => state.filteredGroupsIds,

    isGroupModuleRegistered: (state) => (group_id) => store.hasModule(ns.groupModule(group_id)),
    
    numGroupsWithUnseen: (state) => state.numGroupsWithUnseen,

    isGroupOpened: (state) => (group_id) => state.openedGroupsIds.includes(group_id),

    getAllGroups: (state) => 
    {
        var allGroups = []
        var modules = store._modules.root._children;

        // array of all module names
        modules = Object.keys(modules);

        for (let i in modules){
            if(h.regExpressionMatch(ns.groupModule(), modules[i] )){
                allGroups.push(store._modules.root._children[modules[i]].state) // add current state object of 'ns.groupModule(id)' to arr
            } 
        }

        return allGroups
    },

    // doesnt check if group module accualy exist
    getGroupById: (state) => (id) => 
    {
        return store._modules.root._children[ns.groupModule(id)].state
    },

    // doesnt check if group module accualy exist
    getGroupsById: (state) => (ids) => 
    {
        var groups = []

        for(let i in ids){
            groups.push(store._modules.root._children[ns.groupModule(ids[i])].state)
        }

        return groups
    }
}

export default getters 