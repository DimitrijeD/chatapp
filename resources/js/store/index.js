import Vuex from 'vuex';
import Vue from 'vue';
import auth from "./modules/auth.js";
import groups from "./modules/groups/groups.js";

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        auth,
        groups,
    },
});