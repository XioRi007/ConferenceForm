<script setup>
  import FacebookIcon from '../components/icons/FacebookIcon.vue';
  import TwitterIcon from '../components/icons/TwitterIcon.vue';
  const shareText = import.meta.env.VITE_SHARE_TEXT;
  const url = import.meta.env.VITE_SHARE_URL;
</script>

<template>
    <ShareNetwork
          class="mx-5"
          network="facebook"
          :url="url"
          >
          <FacebookIcon></FacebookIcon>
        </ShareNetwork>
        <ShareNetwork
          class="mx-5"
          network="Twitter"
          :url="url"
          :title="shareText"
          >
          <TwitterIcon></TwitterIcon>
        </ShareNetwork>
</template>