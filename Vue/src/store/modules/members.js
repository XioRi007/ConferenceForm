const state = {
    count:0,
    list:[]
}
// getters
const getters = {
    getMembers(){        
        return state.list;
    },
    getMembersCount(){
        return state.count;
    }
}

// actions
const actions = {
    /**
     * Load to the state members list
     */
    async fetchMembers({commit}){
        const _res = await fetch(`${window.location.origin}/api/members`);
        const res = await _res.json();
        commit('setMembers', res);

    },
    
    /**
     * Load to the state members count
     */
    async fetchMembersCount({commit}){
        const _res = await fetch(`${window.location.origin}/api/members/count`);
        const res = await _res.json();
        commit('setMembersCount', res.membersCount);
    }
}

// mutations
const mutations = {
    setMembers(state, payload){
        state.list = payload;
    },
    setMembersCount(state, payload){
        state.count = payload;
    }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}