const mutations = {
    setUser: (state, user) => state.user = user,

    logout: (state) => state.user = null,
}

export default mutations