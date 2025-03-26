<script setup>
import {reactive, ref} from "vue";
import SlideThumb from "./SlideThumb.vue";
import {faker} from '@faker-js/faker';
import draggable from "vuedraggable";
import {Slide} from "../model/slide.ts";
import api from '../services/api';

const KEYCLOAK_URL = "https://sso.hands-on-technology.org";
const REALM = "master";
const CLIENT_ID = "kiosk";
const REDIRECT_URI = encodeURIComponent("https://kiosk.hands-on-technology.org/auth");

const redirectToKeycloak = () => {
  window.location.href = `${KEYCLOAK_URL}/realms/${REALM}/protocol/openid-connect/auth?client_id=${CLIENT_ID}&response_type=code&redirect_uri=${REDIRECT_URI}`;
};

// Check if user is logged in
const checkAuth = () => {
  const token = localStorage.getItem("jwt_token");
  if (!token || token === "") {
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

const transitionTime = ref(15); // Default transition time in ms
const transitionEffect = ref("ease-in-out"); // Default transition effect

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

async function handleSave() {
  let response = await api.post("/api/scores/set-rounds", JSON.stringify(showRound))
  console.log(response.data)
}
</script>

<template>
  <h1>Kiosk Carousel</h1>

  <div class="controls">
    <div class="show-round">
      <form @submit.prevent="handleSave">
        <label>VR I
          <input v-model="showRound.vr1" name="vr1" type="checkbox">
        </label>
        <label>VR II
          <input v-model="showRound.vr2" name="vr2" type="checkbox">
        </label>
        <label>VR III
          <input v-model="showRound.vr3" name="vr3" type="checkbox">
        </label>
        <label>AF
          <input v-model="showRound.af" name="af" type="checkbox">
        </label>
        <label>VF
          <input v-model="showRound.vf" name="vf" type="checkbox">
        </label>
        <label>HF
          <input v-model="showRound.hf" name="hf" type="checkbox">
        </label>
        <button type="submit">Save</button>
      </form>
    </div>

    <label>Transition Time (sec):
      <input v-model="transitionTime" max="60" min="2" type="range">
      <span>{{ transitionTime }}ms</span>
    </label>

    <label>Transition Effect:
      <select v-model="transitionEffect">
        <option selected value="fade">Fade</option>
        <option value="slide">Slide</option>
        <option value="loop">Loop</option>
      </select>
    </label>
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
