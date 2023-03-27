<script setup>
import WizardComponent from '../components/WizardComponent.vue'
import Map from '../components/Map.vue'
import { onBeforeMount } from 'vue';
import {useStore} from 'vuex'
const store = useStore();

/**
 * При onBeforeMount
 * Если в localStorage хранится id участника - загружает его в state
 * 
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
        <h1 class="mb-5">Register to the conference</h1>  
        <Map></Map>
        <WizardComponent></WizardComponent>        
      </div>
    </div>
</template>
<style>
</style>
