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

function getFormattedDateTime() {
  const now = new Date();

  now.setDate(29)

  // Get date components
  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, '0'); // Months are 0-based
  const day = String(now.getDate()).padStart(2, '0');

  // Get time components
  const hours = String(now.getHours()).padStart(2, '0');
  const minutes = String(now.getMinutes()).padStart(2, '0');

  return `${year}-${month}-${day}+${hours}:${minutes}`;
}

let slides = ref([
  {
    id: 1,
    title: "rg-scores",
    content: new UrlSlideContent("https://kiosk.hands-on-technology.org/screen.html"),
  },
  {
    id: 2,
    title: "currently-running",
    content: new UrlSlideContent("https://flow.hands-on-technology.org/output/zeitplan.cgi?plan=160&role=14&brief=no&output=slide&hours=1&now=" + getFormattedDateTime()),
  },
])
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
