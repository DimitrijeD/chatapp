import Vuex from 'vuex'
import Vue from 'vue'
import auth from "./modules/auth.js"
import groups from "./modules/groups/groups.js"
import users from "./modules/users/users.js"
import chat_rules from "./modules/chat_rules/chat_rules.js"

Vue.use(Vuex)

export default new Vuex.Store({
    modules: {
        auth,
        groups,
        users,
        chat_rules
    },
})