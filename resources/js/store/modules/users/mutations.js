import defaultState_Users from './defaultState_Users.js'

const mutations = 
{
    mergeUsers: (state, users) => state.users = { ...state.users, ...users},

    setFilterForAddUsers: (state, usersIds) => state.filterForAddUsers = usersIds,

    resetState: (state) => Object.assign(state, defaultState_Users()),
    
}

export default mutations