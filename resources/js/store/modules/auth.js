const state = {
    user: null,
}

const getters = {
    StateUser: (state) => state.user,
}

const actions = {
    storeUser: (context, user) => context.commit('setUser', user), 

    logout: ({ commit }) => commit("logout"),

    getUser(context) {
        axios.get('/api/user').then((res)=>{
            context.commit('setUser', res.data)
        })
    },
}

const mutations = {
    setUser: (state, user) => state.user = user,

    logout: (state) => state.user = null,
}

export default {
    state,
    getters,
    actions,
    mutations,
}