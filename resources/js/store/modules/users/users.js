import * as helpers from "../../../helpers/helpers_exporter.js"

import { 
    getByStr,
    excludeByIds,
 } from './getters.js'

// import {  } from './sorters.js'

import { 
    prepareUsersSearchRequest,
} from './modifiers.js'

//----------------------------------------------------------------------------------------------------

const state = 
{
    users: {}, 
    filterForAddUsers:    [], // contains array of users ID-s that match string passed in /ChatWindow/Config/AddUsers input
    // filterForCreateGroup: [], // contains array of users ID-s that match string passed in /CreateChatGroup input
}

const getters = 
{
    getAllUsers: (state) => state.users,

    getAllUsersIds: (state) => Object.keys(state.users),

    getFilterForAddUsers: (state) => state.filterForAddUsers,

    getById: (state) => (id) => state.users[id]
}

const actions = 
{
    searchForAddUsersInApi(context, data)
    {
        let payload = prepareUsersSearchRequest(data, context.getters.getAllUsersIds)

        return axios.post('/api/users/search', payload).then((res) => {
            if(!res.data.length)
                return

            context.commit('mergeUsers', helpers.createHashMap_OneToOne(res.data, 'id'))

            let payload = {
                search_str: data.search_str,
                exclude: data.exclude
            }
            context.dispatch('searchForAddUsersInStore', payload)
        })
    },

    searchForAddUsersInStore(context, data)
    {
        // check if users object has at least one user only then proceed

        let usersIds = getByStr(state.users, data.search_str)
        usersIds = excludeByIds(usersIds, data.exclude)

        context.commit('setFilterForAddUsers', usersIds)
    }

}

const mutations = 
{
    mergeUsers: (state, users) => state.users = { ...state.users, ...users},

    setFilterForAddUsers: (state, usersIds) => state.filterForAddUsers = usersIds,
    
}

export default 
{
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
}