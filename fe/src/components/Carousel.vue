<script setup>
import '@splidejs/vue-splide/css';
import {ref} from "vue";
import {Slide} from "../model/slide.js";
import SlideContentRenderer from "./slides/SlideContentRenderer.vue";
import {ImageSlideContent} from "../model/imageSlideContent.js";

const slides = ref([
  new Slide(1, 'Testbild', new ImageSlideContent('https://www.first-lego-league.org/files/relaunch2022/theme/layout/fll/logo/vertical/FIRSTLego_IconVert_RGB.png')),
  new Slide(1, 'Testbild', new ImageSlideContent('https://www.first-lego-league.org/assets/images/f/FLL_C_Robi_Detail_Girl_BG-3aaabbd0.webp')),
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
