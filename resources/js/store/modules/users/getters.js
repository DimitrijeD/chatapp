const getters = 
{
    getAllUsers: (state) => state.users,

    getAllUsersIds: (state) => Object.keys(state.users),

    getFilterForAddUsers: (state) => state.filterForAddUsers,

    getById: (state) => (id) => state.users[id]
}

export default getters