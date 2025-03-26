<script setup>
import {onMounted, reactive, ref} from "vue";
import SlideThumb from "./SlideThumb.vue";
import {faker} from '@faker-js/faker';
import draggable from "vuedraggable";
import {Slide} from "../model/slide.ts";
import api from '../services/api';
import {UrlSlideContent} from "../model/urlSlideContent.js";

const KEYCLOAK_URL = "https://sso.hands-on-technology.org";
const REALM = "master";
const CLIENT_ID = "kiosk";
const REDIRECT_URI = encodeURIComponent("https://" + document.location.href + "/auth");

const redirectToKeycloak = () => {
  window.location.href = `${KEYCLOAK_URL}/realms/${REALM}/protocol/openid-connect/auth?client_id=${CLIENT_ID}&response_type=code&redirect_uri=${REDIRECT_URI}`;
};

function isValidJwt(token) {
  if (!token) return false;

  try {
    // Split the token into parts
    const payloadBase64 = token.split('.')[1];
    if (!payloadBase64) return false;

    // Decode the payload
    const payload = JSON.parse(atob(payloadBase64));

    // Check for expiration timestamp (exp)
    const expiration = payload.exp * 1000; // Convert to milliseconds
    const now = Date.now();

    return expiration > now;
  } catch (e) {
    console.error("Invalid JWT token:", e.message);
    return false;
  }
}

// Check if user is logged in
const checkAuth = () => {
  const token = localStorage.getItem("jwt_token");
  if (!token || !isValidJwt(token)) {
    redirectToKeycloak();
  }
};

checkAuth();

//const socket = inject('websocket');
//const slides = socket.slides;
let slides

function addSlide() {

  const randomName = ref(faker.person.fullName());
  slides.value.push(new Slide(slides.value.length + 1, randomName));
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

let slide = new Slide(1, "title", UrlSlideContent)

async function pushSlide() {
  let d = new FormData
  d.set("title", slide.title)
  d.set("content", JSON.stringify(slide.content))
  await api.post("/api/events/1/settings", d)
}

onMounted(fetchRounds())
onMounted(fetchSettings())
</script>

<template>
  <h1>Kiosk Carousel</h1>
  <button @click="pushSlide"></button>
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
    <div class="add-slide" @click="addSlide">
      <fa :icon="['fas', 'plus-circle']"></fa>
    </div>
    <draggable v-model="slides" class="draggable-list" ghost-class="ghost" group="slides" item-key="id"
               @end="updateOrder">
      <template #item="{ element }">
        <SlideThumb :slide="element" @deleteSlide="deleteSlide(element)"></SlideThumb>
      </template>
    </draggable>
  </div>
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
