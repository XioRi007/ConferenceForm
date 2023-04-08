<script setup>    
    import { ref, computed, onBeforeMount, onUnmounted } from 'vue';
    import {useStore} from 'vuex'
    import PersonalForm from '../components/PersonalForm.vue'
    import DetailedForm from '../components/DetailedForm.vue'
    import ShareLinks from '../components/ShareLinks.vue'


    const store = useStore();
    const step = ref(1);
    const completed = computed(()=>step.value >= maxStep);
    const maxStep = 3;
    const stepperProgress = computed(()=>(100) * (completed.value ? 1: step.value-1) + '%');
    const membersCount = ref(0);

    /**
     * If localStorage contains step - loads it to state
     * Loads membersCount
     */
    onBeforeMount(async () => {
            const _step = localStorage.getItem('step');
            if(_step != null){
                step.value = parseInt(_step);
            }
            if(step.value == maxStep){
                step.value = 1;
                localStorage.setItem('step', 1);
            }
            await store.dispatch('members/fetchMembersCount');
            membersCount.value = store.getters['members/getMembersCount'];
    });
    
    /**
     * Clears state and localStorage
     */
    onUnmounted(()=>{
        if(completed.value == true){
            localStorage.clear();
            store.commit('register/clear');
        }
    })
    
    /**
     * Step back
     * Saves to localStorage new step
     */
    const backClick = (e) => {
        step.value --;    
        localStorage.setItem('step', step.value);
    }
    /**
     * Step forward (if not the last)
     * Saves to localStorage new step
     */
    const nextClick = (e) => {
        if(step.value != maxStep){            
            step.value ++;    
            localStorage.setItem('step', step.value);
        }
    }
</script>
<template> 
<div class="row justify-content-center">
  <div class="wrapper-stepper col col-lg-9 py-5 p-lg-5">
          <div class="stepper">
              <div class="stepper-progress">
                  <div class="stepper-progress-bar" :style="'width:' + stepperProgress "></div>
              </div>
  
              <div class="stepper-item" :class="{ 'current': step == item, 'success': step > item }" v-for="item in 2" :key="item">
                  <div class="stepper-item-counter">
                      <img class="icon-success" src="https://www.seekpng.com/png/full/1-10353_check-mark-green-png-green-check-mark-svg.png" alt="">
                      <span class="number">
                          {{ item }}
                      </span>
                  </div>
                  <span class="stepper-item-title">
                      Step {{ item }}
                  </span>
              </div>
          </div>
          <div v-if="!completed" class="row justify-content-center">
            <PersonalForm v-if="step==1" :on-next="nextClick" class="col-9"></PersonalForm>
            <DetailedForm v-if="step==2" :on-next="nextClick" :on-back="backClick" class="col-9"></DetailedForm>
          </div>
          <div v-else>
            <h1 class="my-5">You have successfully registered!</h1>
            <div class="row justify-content-center mb-5 bg-body">
            <div class="col-9 justify-center">
                <h4 class="">Share our conference with others:</h4>
                <ShareLinks></ShareLinks>
                <div class="row mt-5">
                <RouterLink to="/members" class="col text-center" >All members({{ membersCount }})</RouterLink>
            </div>
          </div>

        </div>
      </div>  
        
      </div>
      
      
      
</div>
</template>
<style>

.tx-green-1 {
  color: #75cc65;
  font-weight: 600;
}
.wrapper-stepper {
  background-color: #fff;
  border-radius: 32px;
  box-shadow: rgba(0, 0, 0, 0.09);
}
.stepper {
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: relative;
  z-index: 0;
  margin: 0 5vh;
  margin-bottom: 24px;
}
.stepper-progress {
  position: absolute;
  background-color: #c5c5c5;
  height: 2px;
  z-index: -1;
  left: 0;
  right: 0;
  margin: 0 auto;
}
.stepper-progress-bar {
  position: absolute;
  left: 0;
  height: 100%;
  width: 0%;
  background-color: #75cc65;
  transition: all 500ms ease;
}
.stepper-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  color: #c5c5c5;
  transition: all 500ms ease;
}
.stepper-item-counter {
  height: 68px;
  width: 68px;
  display: grid;
  place-items: center;
  background-color: #fff;
  border-radius: 100%;
  border: 2px solid #c5c5c5;
  position: relative;
}
.stepper-item-counter .icon-success {
  position: absolute;
  opacity: 0;
  transform: scale(0);
  width: 24px;
  transition: all 500ms ease;
}
.stepper-item-counter .number {
  font-size: 22px;
  transition: all 500ms ease;
}
.stepper-item-title {
  position: absolute;
  font-size: 14px;
  bottom: -24px;
}
.stepper-item.success .stepper-item-counter {
  border-color: #75cc65;
  background-color: #c8ebc1;
  color: #fff;
  font-weight: 600;
}
.stepper-item.success .stepper-item-counter .icon-success {
  opacity: 1;
  transform: scale(1);
}
.stepper-item.success .stepper-item-counter .number {
  opacity: 0;
  transform: scale(0);
}
.stepper-item.success .stepper-item-title {
  color: #75cc65;
}
.stepper-item.current .stepper-item-counter {
  border-color: #75cc65;
  background-color: #75cc65;
  color: #fff;
  font-weight: 600;
}
.stepper-item.current .stepper-item-title {
  color: #818181;
}
.stepper-pane {
  color: #333;
  text-align: center;
  padding: 120px 60px;
  box-shadow: 0 8px 12px rgba(0, 0, 0, 0.09);
  margin: 40px 0;
}
.controls {
  display: flex;
  justify-content: space-between;
  flex-direction: row-reverse;
}

</style>
