<script setup>
import '@splidejs/vue-splide/css';
import {ref} from "vue";
import {Slide} from "../model/slide.js";
import SlideContentRenderer from "./slides/SlideContentRenderer.vue";

const slides = ref([
  new Slide(1, 'Testbild'),
]);
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
      <SlideContentRenderer :slide="slide" />
    </SplideSlide>
  </Splide>
</template>

<style scoped>

</style>
