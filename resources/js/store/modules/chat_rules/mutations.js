import defaultState_chat_rules from "./defaultState_chat_rules"

const mutations = 
{
    setChatRules:  (state, val) => state.rules      = val,
    setKeys:       (state, val) => state.keys       = val,
    setRoles:      (state, val) => state.roles      = val,
    setGroupTypes: (state, val) => state.groupTypes = val,

    init_num_messages:     (state, val) => state.init_num_messages     = val,
    earliest_num_messages: (state, val) => state.earliest_num_messages = val,

    resetState: (state) => Object.assign(state, defaultState_chat_rules()),
}

export default mutations