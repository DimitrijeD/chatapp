import * as ns from '../../../store/module_namespaces.js'

const actions = {
    storeUser: (context, user) => {
        context.commit('setUser', user)
    }, 

    logout: (context) => {
        context.dispatch(ns.groupsManager() + '/purgeAllChatData', null, {root:true}).then(() => {
            context.dispatch(ns.users()      + '/resetState', null, {root:true})
            context.dispatch(ns.chat_rules() + '/resetState', null, {root:true})
            context.commit("logout")
        }).catch(e => {
            context.commit("logout")
        })
    },

    getUser(context) {
        axios.get('/api/user').then((res)=>{
            context.dispatch('storeUser', res.data)
        })
    },
}

export default actions