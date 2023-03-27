<template>
    <GoogleMap
      api-key="AIzaSyByH7hN0PE5FB6Nur-VNjhMua-QdBbOV90"
      style="width: 100%; height: 450px"
      :center="center"
      :zoom="15"
    >      <Marker :options="{ position: center, title, label}" />
    </GoogleMap>
  </template>
  
  <script setup>
      import { GoogleMap, Marker } from "vue3-google-map";
      const center = { lat: 34.10120410261573, lng: -118.34365141546802 }; 
      const title = "7060 Hollywood Blvd, Los Angeles, CA";
      const label = 'H'
  </script>



