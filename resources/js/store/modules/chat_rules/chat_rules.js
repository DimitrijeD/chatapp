const state = {
    rules: null,
    keys: null,
    roles: null,
    groupTypes: null
}

const getters = 
{
    StateRules: (state) => state.rules,

    StateKeys: (state) => state.keys,

    StateRoles: (state) => state.roles,

    StateGroupTypes: (state) => state.groupTypes,
}

const actions = 
{
    FetchRules(context)
    {
        if(!state.rules){
            axios.get('/api/chat/role-rules/get').then((res) => {
                context.commit('setChatRules', res.data.chat_rules)
                context.commit('setKeys', res.data.keys)
                context.commit('setRoles', res.data.roles)
                context.commit('setGroupTypes', res.data.groupTypes)
            })
        } 

    },

}

const mutations = 
{
    setChatRules: (state, chat_rules) => state.rules = chat_rules,

    setKeys: (state, keys) => state.keys = keys,

    setRoles: (state, roles) => state.roles = roles,

    setGroupTypes: (state, groupTypes) => state.groupTypes = groupTypes,

}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
}