<script setup>
import '@splidejs/vue-splide/css';
import {inject, ref} from "vue";
import SlideContentRenderer from "./slides/SlideContentRenderer.vue";
import {UrlSlideContent} from "../model/urlSlideContent.js";
import {ImageSlideContent} from "../model/imageSlideContent.js";
import qrPlan from "../assets/imgSlides/qr-plan.png";


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
  {
    id: 3,
    title: "plan-qr",
    content: new ImageSlideContent(qrPlan),
  },
])
</script>

<template>
  <Splide :options="{
    autoplay: true,
    rewind: true,
    interval: 2000,
    type: 'fade',
    arrows: false,
    pauseOnHover: false,
    pauseOnFocus: false,
    pagination: false,
  }" aria-label="My Favorite Images">
    <SplideSlide v-for="slide in slides" :key="slide.id">
      <SlideContentRenderer :slide="slide" class="slide"/>
    </SplideSlide>
  </Splide>
  <footer>hier kommen noch Logos hin ...</footer>
</template>

<style scoped>
footer {
  background-color: white;
  width: 100vw;
  height: 10vh;
  position: fixed;
  z-index: 10000;
  bottom: 0;
}

.slide {
  width: 100vw;
  height: 90vh;
  position: relative;
  margin: 0;
  padding: 0;
  overflow: hidden;
}
</style>
