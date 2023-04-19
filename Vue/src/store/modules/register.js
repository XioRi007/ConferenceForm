// initial state
const getDefaultState = () => {
    return {
        personal:{  
            id:null,      
            firstName: '',
            lastName: '',
            birthdate: '',
            reportSubject: '',
            country: 'Ukraine',
            phone: '+',
            email: '',
        },
        detailed:{
            company:'',
            position:'',
            about:'',
            photo:''
        }
    }
  }
const state = getDefaultState();

// getters
const getters = {
    getPersonal(){
        return state.personal;
    },
    getId(){
        return state.personal.id;
    },
    getDetailed(){
        return state.detailed;
    }
}

// actions
const actions = {
    /**
     * Loads to state personal information
     */
    async loadPersonal({commit}){
        const _res = await fetch(`${window.location.origin}/api/user/personal/${state.personal.id}`);
        const res = await _res.json();
        for (const [name, value] of Object.entries(res)) {
            commit('setPersonal', {
                name,
                value
            })
        }
    },

    /**
     * Loads to state detail information
     */
    async loadDetails({commit}){
        const _res = await fetch(`${window.location.origin}/api/user/details/${state.personal.id}`);
        const res = await _res.json();
        if(res.error){
            throw new Error({message:res.error});
        }
        for (const [name, value] of Object.entries(res)) {
            commit('setDetails', {
                name,
                value: value != 'null' ? value: ''
            })
        }
    },
    
    /**
     * Register member
     */
    async registerParticipant(){      
        let formData = new FormData();
        formData.append('first_name', state.personal.firstName);
        formData.append('last_name', state.personal.lastName);
        formData.append('birthdate', state.personal.birthdate);
        formData.append('report_subject', state.personal.reportSubject);
        formData.append('country', state.personal.country);
        formData.append('phone', state.personal.phone);
        formData.append('email', state.personal.email);
        const _res = await fetch(`${window.location.origin}/api/register`, {
            method:'POST',
            body:formData
        });
        const res = await _res.json(); 
        if(!res.error){
            state.personal.id = res.id;
        }
        else{
            console.log(res);
            if(res.validation){
                throw res.validation;
            }
            throw new Error(res.error);
        }        
    },

    /**
     * Updating member's personal info
     */
    async updateParticipant(){
        let formData = new FormData();
        formData.append('id', state.personal.id);
        formData.append('first_name', state.personal.firstName);
        formData.append('last_name', state.personal.lastName);
        formData.append('birthdate', state.personal.birthdate);
        formData.append('report_subject', state.personal.reportSubject);
        formData.append('country', state.personal.country);
        formData.append('phone', state.personal.phone);
        formData.append('email', state.personal.email);
        const _res = await fetch(`${window.location.origin}/api/update`, {
            method:"POST",
            body:formData
        });
        const res = await _res.json();        
        if(res.error){
            console.log(res);
            if(res.validation){
                throw res.validation;
            }
            throw new Error(res.error);
        }
    },
    
    /**
     * Updating member's detail info
     * 
     */
    async updateDetails(){
        let formData = new FormData();
        formData.append('file', state.detailed.photo);
        formData.append('company', state.detailed.company);
        formData.append('position', state.detailed.position);
        formData.append('about', state.detailed.about);
        formData.append('id', state.personal.id);
        const _res = await fetch(`${window.location.origin}/api/update`, {
            method:"POST",
                body:formData
        });
        const res = await _res.json();      
        if(res.error){
            console.log(res.error);
            throw new Error(res.error);
        }
    }
}

// mutations
const mutations = {
    setPersonal(state, payload){
        state.personal[payload.name] = payload.value;
    },
    setDetails(state, payload){
        state.detailed[payload.name] = payload.value;
    },
    clear(state){
        Object.assign(state, getDefaultState());
    }
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
}