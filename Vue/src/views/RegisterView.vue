<script setup>
import WizardComponent from '../components/WizardComponent.vue'
import Map from '../components/Map.vue'
import { onBeforeMount } from 'vue';
import {useStore} from 'vuex'
const store = useStore();

/**
 * If localStorage contains member's id - sets it to the store
 */
 onBeforeMount(() => {
  const savedUser = localStorage.getItem('user');
  if (savedUser !== null) {    
    store.commit('register/setPersonal', {
      name:'id',
      value:savedUser
    });
  }
});
</script>

<template>
    <div class="container my-5 text-center">
      <div class="border shadow p-5"> 
        <Map></Map>
        <h1 class="my-5">To participate in the conference, please fill out the form</h1> 
        <WizardComponent></WizardComponent>        
      </div>
    </div>
</template>
<style>
</style>
