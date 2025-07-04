<script setup>
import '@splidejs/vue-splide/css';
import {inject, onMounted, reactive, ref} from "vue";
import SlideContentRenderer from "./slides/SlideContentRenderer.vue";
import logo1_cut from "../../assets/img/logo1_cut.png";
import logo2_cut from "../../assets/img/logo2_cut.png";
import logo3_cut from "../../assets/img/logo3_cut.png";
import logo4 from "../../assets/img/logo4.png";
import api from "../../services/api.js";
import {Slide} from "../../model/slide.ts";
import {ImageSlideContent} from "../../model/imageSlideContent.ts";
import {RobotGameSlideContent} from "../../model/robotGameSlideContent.ts";
import {UrlSlideContent} from "../../model/urlSlideContent.ts";
import {SlideContent} from "../../model/slideContent.ts";

/*const socket = inject('websocket');
socket.registerClient();
socket.addListener((msg) => {
  if (msg.type === 'pushSlide') {
    slide.value = Slide.fromObject(msg.slide);
    showSlide.value = true
  }
});
*/
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

/*let slides = ref([
    new Slide(1, "rg-scores", new UrlSlideContent("https://kiosk.hands-on-technology.org/screen.html")),
    new Slide(2, "currently-running", new UrlSlideContent("https://flow.hands-on-technology.org/output/zeitplan.cgi?plan=160&role=14&brief=no&output=slide&hours=1&now=" + getFormattedDateTime())),
    new Slide(3, "plan-qr", new ImageSlideContent(qrPlan)),
])*/

let slides = ref([])

let settings = reactive({
  transitionTime: 15,
  transitionEffect: "fade",
})

let loaded = ref(false)

function getSlide(slide) {
  let types = {
    image: new ImageSlideContent("").toJSON().type,
    rg: new RobotGameSlideContent().toJSON().type,
    url: new UrlSlideContent("").toJSON().type
  }

  let content = JSON.parse(slide.content)
  switch (content.type) {
    case types.image:
      return new Slide(slide.id, slide.title, new ImageSlideContent(content.url))
    case types.rg:
      return new Slide(slide.id, slide.title, new RobotGameSlideContent())
    case types.url:
      return new Slide(slide.id, slide.title, new UrlSlideContent(content.url))
  }
}

let slide = ref()
let showSlide = ref(false)

async function fetchSlides() {
  const response = await api.get("/api/events/1/slides")
  if (response && response.data) {
    slides = []
    for (let slide of response.data) {
      slides.push(getSlide(slide))
    }
    setTimeout(function () {
      loaded.value = true
    }, 1000)
    slideKey.value++
  }
}

async function fetchSettings() {
  try {
    const response = await api.get("/api/events/1/settings")
    if (response && response.data) {
      Object.keys(settings).forEach((key) => {
        settings[key] = response.data[key]
      })
    }
  } catch (error) {
    console.log("Error fetching settings: ", error.message)
  }
}

function startFetchingSlides() {
  setInterval(function () {
    fetchSlides()
  }, 600000)
}

onMounted(fetchSettings)
//onMounted(startFetchingSlides)
//onMounted(fetchSlides)

</script>

<template>
  <SlideContentRenderer v-if="showSlide === true" :slide="slide" class="slide"/>
  <footer>
    <div>
      <img :src="logo1_cut" alt="logo">
    </div>
    <div>
      <img :src="logo2_cut" alt="logo">
    </div>
    <div>
      <img :src="logo3_cut" alt="logo">
    </div>
    <div>
      <img :src="logo4" alt="logo">
    </div>
  </footer>
</template>

<style scoped>
footer {
  background-color: white;
  width: 100vw;
  height: 10vh;
  position: fixed;
  z-index: 10000;
  bottom: 0;
  display: flex;
  align-items: center;
  justify-content: space-evenly;
}

footer div {
  padding-left: 2rem;
  padding-right: 2rem;
}

footer img {
  max-height: 9vh;
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
