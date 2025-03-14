<script setup>
import '@splidejs/vue-splide/css';
import {inject, ref} from "vue";
import SlideContentRenderer from "./slides/SlideContentRenderer.vue";

const socket = inject('websocket');
socket.registerClient();
socket.addListener((msg) => {
  console.log("msg in carusell: ", msg);
  // TODO do some specific listening here (e.g. pausing or setting the delay)
});

let slides = ref(socket.slides);
</script>

<template>
  <Splide :options="{
    autoplay: true,
    rewind: true,
    interval: 5000,
    type: 'fade',
    arrows: false,
    pauseOnHover: false,
    pauseOnFocus: false,
  }" aria-label="My Favorite Images">
    <SplideSlide v-for="slide in slides" :key="slide.id">
      <SlideContentRenderer :slide="slide"/>
    </SplideSlide>
  </Splide>
</template>

<style scoped>

</style>
