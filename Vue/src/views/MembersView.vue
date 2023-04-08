<script setup>
  import { onBeforeMount, ref } from 'vue';
  import { useStore } from 'vuex';

  const store = useStore();
  const members = ref([]);
  const img = ref('');
  onBeforeMount(async() => {
    await store.dispatch('members/fetchMembers');
    members.value = store.getters['members/getMembers'];
  });
  
</script>

<template>
  <div class="container my-5 border shadow p-5 pt-0">
    <h1 class="text-center my-5">All members</h1>
    <div class="table-responsive">
    <table class="table align-middle ">
    <thead>
      <tr>
        <th scope="col">Photo</th>
        <th scope="col">Full Name</th>
        <th scope="col">Report Subject</th>
        <th scope="col">Email</th>
      </tr>
    </thead>
    <tbody class="table-group-divider ">
      <tr v-for="member in members" :key="member.id">
        <td><img :src="member.photo" class="rounded-circle object-fit-cover" width="50" height="50"></td>
        <td>{{ member.fullName}}</td>
        <td>{{ member.reportSubject }}</td>
        <td><a href="{{ member.email }}">{{ member.email }}</a></td>
      </tr>
    </tbody>
  </table></div>
  </div>
  
</template>



<style scoped>
.members-table {
  margin-top: 1rem;
}
</style>

