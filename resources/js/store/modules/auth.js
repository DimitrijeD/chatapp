import axios from "axios";

const state = {
  user: null,
};


const getters = {
    StateUser: (state) => state.user,
    
};

const actions = {
    async getUser(context) {
        await axios.get('/api/user').then((res)=>{
            context.commit('setUser', res.data);
        });
    },

    async LogOut({ commit }) {
        let user = null;
        commit("logout", user);
    },
};

const mutations = {
    setUser(state, user)
    {
        state.user = user; 

    },

    logout(state, user) {
        state.user = user;
    },
};

export default {
  state,
  getters,
  actions,
  mutations,
};