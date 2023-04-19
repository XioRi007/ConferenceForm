<script setup>
    import { useStore } from 'vuex'
    import {computed, reactive, onMounted, ref } from 'vue';
    const store = useStore();
    const props = defineProps(['onNext', 'onBack']);
    const error = ref('');
    const detailedForm = reactive(store.getters['register/getDetailed']);
    const id = computed(()=>store.getters['register/getId'])

    /**
     * Input handler
     */
    const setDetailed = (e) =>{
        store.commit('register/setDetails', {
            name:e.target.name,
            value:e.target.value
        })
    }
    
    /**
     * If store contains member's id
     * Loads details
     */
    onMounted(async ()=>{
        try{
            if(id != null){  
                await store.dispatch('register/loadDetails');
            }
        }
        catch(err){
            error.value = err.message;
        }
    });

    /**
     * Updating member
     * 
     */
    const submit = async() => {
        try {
            const user = store.getters['register/getId'];
            if(user != null){
            await store.dispatch('register/updateDetails');
            }        
        } catch (err) {
            error.value = err.message;        
        }
    }
    const backClick = (e) => {
        e.preventDefault();
        submit();
        props.onBack();
    }

    const nextClick = (e) => {
        e.preventDefault();
        submit();
        props.onNext();
    }
    /**
     * Saves image to the store
     * 
     */
    const setPhoto = (event) => {
        store.commit('register/setDetails', {
            name:'photo',
            value:event.target.files[0]
        })
    }

</script>

<template>
    <form>
    <div class="row mb-3">
        <label  class="col-sm-3 col-form-label" for="company">Company</label>
        <div class="col-sm-9">
            <input id="company" name="company" maxlength="50" class="form-control" type="text" v-model="detailedForm.company" @input="setDetailed"/>
        </div>
    </div>
    <div class="row mb-3">
        <label  class="col-sm-3 col-form-label" for="position">Position</label>
        <div class="col-sm-9">
            <input id="position" name="position" maxlength="50" class="form-control" type="text" v-model="detailedForm.position" @input="setDetailed"/>
        </div>
    </div>
    <div class="row mb-3">
        <label  class="col-sm-3 col-form-label" for="about">About</label>
        <div class="col-sm-9">
            <textarea id="about" name="about" class="form-control" rows="5" v-model="detailedForm.about" @input="setDetailed"></textarea>

        </div>
    </div>
    <div class="row mb-3">
        <label  class="col-sm-3 col-form-label" for="photo">Photo</label>
        <div class="col-sm-9">
            <input id="photo" name="photo" class="form-control" type="file" @input="setPhoto">
        </div>
    </div>    
    <div class="row mb-3 text-danger text-left">
        <p>* - Required</p>
    </div>
    <div class="row mb-3 text-danger" v-if="error != ''">
        <p>{{ error }}</p>
    </div>    
    <div class="controls">
        <button class="btn btn-success" @click="nextClick" type="submit">
            Complete
        </button>
        <button class="btn btn-outline-success" @click="backClick" type="submit">
            Back
        </button>
    </div>
</form>
</template>


<style>
</style>
