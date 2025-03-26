<script setup>
import '@splidejs/vue-splide/css';
import {inject, ref} from "vue";
import SlideContentRenderer from "./slides/SlideContentRenderer.vue";
import {UrlSlideContent} from "../model/urlSlideContent.js";

/*const socket = inject('websocket');
socket.registerClient();
socket.addListener((msg) => {
  console.log("msg in carusell: ", msg);
  // TODO do some specific listening here (e.g. pausing or setting the delay)
});

let slides = ref(socket.slides);*/
let urlSlideContent = new UrlSlideContent()
let slides = ref([
  {
    id: 1,
    title: "rg-scores",
    content: new UrlSlideContent("https://kiosk.hands-on-technology.org/screen.html"),
  },
  {
    id: 2,
    title: "currently-running",
    content: new UrlSlideContent("https://planning.hands-on-technology.org/output/zeitplan.cgi?plan=160&role=14&brief=no&output=slide&hours=1&now=" + Date.now()),
  },
])
</script>

<template>
  <Splide :options="{
    autoplay: true,
    rewind: true,
    interval: 5000,
    type: 'fade',
    arrows: true,
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
