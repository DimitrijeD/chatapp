
const actions = 
{
    setupRules({commit}, chat_rules)
    {
        commit('setChatRules',  chat_rules.chat_rules)
        commit('setKeys',       chat_rules.keys      )
        commit('setRoles',      chat_rules.roles     )
        commit('setGroupTypes', chat_rules.groupTypes)
        commit('init_num_messages',     chat_rules.init_num_messages    )
        commit('earliest_num_messages', chat_rules.earliest_num_messages)
    },

    resetState({ commit })
    {
        commit('resetState')
    }
}

export default actions