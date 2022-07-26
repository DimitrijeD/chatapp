import Vuex from 'vuex'
import Vue from 'vue'
import auth from "./modules/auth/auth.js"
import groups_manager from "./modules/groups_manager/groups_manager.js"
import users from "./modules/users/users.js"
import chat_rules from "./modules/chat_rules/chat_rules.js"

Vue.use(Vuex)
Vue.config.devtools = true

export default new Vuex.Store({
    modules: {
        auth,
        groups_manager,
        users,
        chat_rules,
    },
})