import * as h from "../../functions/helpers.js"

const actions = 
{
    searchForAddUsersInApi({ commit, dispatch, getters }, data)
    {
        let payload = h.prepareUsersSearchRequest(data, getters.getAllUsersIds)

        return axios.post('/api/users/search', payload).then((res) => {
            if(!res.data.length) return

            commit('mergeUsers', h.createDict(res.data, 'id'))

            let payload = {
                search_str: data.search_str,
                exclude: data.exclude
            }
            dispatch('searchForAddUsersInStore', payload)
        })
    },

    searchForAddUsersInStore({ state, commit }, data)
    {
        // check if users object has at least one user only then proceed

        let usersIds = h.getByStr(state.users, data.search_str)
        usersIds = h.excludeByIds(usersIds, data.exclude)

        commit('setFilterForAddUsers', usersIds)
    },

    resetState({ commit })
    {
        commit('resetState')
    },

}

export default actions