<script setup>
import {inject, onMounted, reactive, ref} from "vue";
import SlideThumb from "./SlideThumb.vue";
import draggable from "vuedraggable";
import {Slide} from "../../model/slide.ts";
import api from '../../services/api.js';
import {UrlSlideContent} from "../../model/urlSlideContent.ts";
import ChooseSlideType from "./ChooseSlideType.vue";

const KEYCLOAK_URL = "https://sso.hands-on-technology.org";
const REALM = "master";
const CLIENT_ID = "kiosk";
const REDIRECT_URI = encodeURIComponent("https://kiosk.hands-on-technology.org/auth");

const redirectToKeycloak = () => {
  window.location.href = `${KEYCLOAK_URL}/realms/${REALM}/protocol/openid-connect/auth?client_id=${CLIENT_ID}&response_type=code&redirect_uri=${REDIRECT_URI}`;
};

let newSlide = ref(false)

function isValidJwt(token) {
  if (!token) return false;

  try {
    // Split the token into parts
    const payloadBase64 = token.split('.')[1];
    if (!payloadBase64) return false;

    // Decode the payload
    const payload = JSON.parse(atob(payloadBase64));

    // Check for expiration timestamp (exp)
    const expiration = payload.iss + payload.exp * 1000; // Convert to milliseconds
    const now = Date.now() / 1000;

    console.log(expiration)
    console.log(payload)
    console.log(now)
    return expiration > now;
  } catch (e) {
    console.error("Invalid JWT token:", e.message);
    return false;
  }
}
/*
function parseJwt(token) {
  try {
    const base64Url = token.split('.')[1];
    const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
    const jsonPayload = decodeURIComponent(
        atob(base64)
            .split('')
            .map(c => '%' + c.charCodeAt(0).toString(16).padStart(2, '0'))
            .join('')
    );
    return JSON.parse(jsonPayload);
  } catch (e) {
    return null;
  }
}

function isJwtExpired(token) {
  const payload = parseJwt(token);
  if (!payload || !payload.exp) return true;

  const now = Math.floor(Date.now() / 1000); // current Unix time in seconds
  return payload.exp < now;
}

function isJwtValid(token) {
  console.log("ey")
  console.log(token)
  console.log(token.split('.'))
  if (!token || token.split('.').length !== 3) return false;
console.log("length ok")
  const payload = parseJwt(token);
  if (!payload || !payload.exp) return false;
console.log("parsing ok")
  return !isJwtExpired(token);
}
*/
// Check if user is logged in
const checkAuth = () => {
  //if (document.location.host === "localhost:5173") return true;
  const token = localStorage.getItem("jwt_token");
  if (!token || !isValidJwt(token)) {
    redirectToKeycloak();
  }
};

checkAuth();

//const socket = inject('websocket');
//const slides = socket.slides;

function addSlide() {
  slides.push(new Slide(slides.length + 1, "randomName", new UrlSlideContent("url")));
}

function updateOrder() {
  console.log("Updated order:", slides); // Logs new order for debugging
}

function deleteSlide(slide) {
  console.log("Deleting slide", slide.title);
  slides.value = slides.value.filter(s => s.id !== slide.id);
}

const showRound = reactive({
  vr1: false,
  vr2: false,
  vr3: false,
  af: false,
  vf: false,
  hf: false,
});

async function saveRoundDisplaySetting() {
  let d = new FormData
  Object.keys(showRound).forEach((key) => {
    d.set(key, showRound[key])
  })
  await api.post("/api/events/1/scores/show-rounds", d)
}

async function fetchRounds() {
  try {
    const response = await api.get("/api/events/1/scores/show-rounds")
    if (response && response.data) {
      Object.keys(showRound).forEach((key) => {
        showRound[key] = response.data[key] || false
      })
    }
  } catch (error) {
    console.error("Error fetching rounds:", error.message);
  }
}

let settings = reactive({
  transitionTime: 15,
  transitionEffect: "fade",
})

async function saveSettings() {
  let d = new FormData
  Object.keys(settings).forEach((key) => {
    d.set(key, settings[key])
  })
  await api.post("/api/events/1/settings", d)
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

let slides = reactive([])

async function fetchSlides() {
  const response = await api.get("/api/events/1/slides")
  if (response && response.data) {
    slides = []
    for (let slide of response.data) {
      slides.push({
        id: slide.id,
        title: slide.title,
        content: JSON.parse(slide.content)
      })
    }
  }
}

function chooseNewSlide() {
  newSlide.value = true
}

const socket = inject('websocket');
const sendMessage = inject('sendMessage');

function sendUpdateMessage() {
  if (sendMessage) {
    sendMessage({ type: 'saveSlides', slides: slides });
    console.log("Message sent to Carousel");
  } else {
    console.error("Send function not available");
  }
}

onMounted(fetchSlides)
onMounted(fetchRounds)
onMounted(fetchSettings)
</script>

<template>
  <h1>Kiosk Carousel</h1>
  <button @click="sendUpdateMessage">Notify Carousel</button>
  <div class="controls">
    <div class="show-round">
      <form @submit.prevent="saveRoundDisplaySetting">
        <label>VR I
          <input v-model="showRound.vr1" name="vr1" type="checkbox">
        </label>
        <br>
        <label>VR II
          <input v-model="showRound.vr2" name="vr2" type="checkbox">
        </label>
        <br>
        <label>VR III
          <input v-model="showRound.vr3" name="vr3" type="checkbox">
        </label>
        <br>
        <label>AF
          <input v-model="showRound.af" name="af" type="checkbox">
        </label>
        <br>
        <label>VF
          <input v-model="showRound.vf" name="vf" type="checkbox">
        </label>
        <br>
        <label>HF
          <input v-model="showRound.hf" name="hf" type="checkbox">
        </label>
        <br>
        <button type="submit">Save</button>
      </form>
    </div>

    <div>
      <form @change="saveSettings">
        <label>Transition Time:
          <input v-model="settings.transitionTime" max="60" min="2" type="range">
          <span>{{ settings.transitionTime }} s</span>
        </label>

        <label>Transition Effect:
          <select v-model="settings.transitionEffect">
            <option selected value="fade">Fade</option>
            <option value="slide">Slide</option>
            <option value="loop">Loop</option>
          </select>
        </label>
      </form>
    </div>
  </div>


  <div class="slides-container">
    <div class="add-slide" @click="chooseNewSlide">
      <fa :icon="['fas', 'plus-circle']"></fa>
    </div>
    <draggable v-model="slides" class="draggable-list" ghost-class="ghost" group="slides" item-key="id"
               @end="updateOrder">
      <template #item="{ element }">
        <SlideThumb :slide="element" @deleteSlide="deleteSlide(element)"></SlideThumb>
      </template>
    </draggable>
  </div>
  <ChooseSlideType v-if="newSlide"/>
</template>

<style scoped>
.controls {
  margin-bottom: 10px;
  padding: 10px;
  background: #f9f9f9;
  border-radius: 5px;
  display: flex;
  gap: 20px;
}

.draggable-list {
  display: flex;
  flex-wrap: wrap;
  padding: 10px;
  background: #f4f4f4;
  border-radius: 8px;
}

.add-slide {
  width: 5rem;
  height: 9rem;
  background-color: lightgreen;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
  font-size: 2rem;
  color: white;
  margin: .5rem;
}

/*.slide-thumb {
  padding: 10px;
  background: white;
  border-radius: 5px;
  border: 1px solid #ddd;
  cursor: grab;
  text-align: center;
}*/

.ghost {
  opacity: 0.5;
}

.chosen {
  background: lightblue;
}
</style>
