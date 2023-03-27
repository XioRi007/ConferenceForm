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
            phone: '+10000000000',
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
     * Загружает в state персональную информацию
     * 
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
     * Загружает в state дополнительную информацию
     * 
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
     * Регистрирует участника на сервере
     * 
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
            console.log(res.error);
            if(res.error.includes('1062')){                
                throw new Error('Member with this email already exists');
            }
            throw new Error('Unexpected error');
        }
        
    },

    /**
     * Обновляет персональную информацию
     * 
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
            console.log(res.error);
            if(res.error.includes('1062')){                
                throw new Error('Member with this email already exists');
            }
            throw new Error('Unexpected error');
        }
    },
    
    /**
     * Обновляет дополнительную информацию
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
            if(res.error.includes('1062')){                
                throw new Error('Member with this email already exists');
            }
            throw new Error('Unexpected error');
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