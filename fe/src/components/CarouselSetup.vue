<script setup>
import {ref} from "vue";
import SlideThumb from "./SlideThumb.vue";
import {faker} from '@faker-js/faker';
import draggable from "vuedraggable";

const slides = ref([
  {
    title: 'John Doe',
    id: 1,
  },
  {
    title: 'Britney',
    id: 2
  },
  {
    title: 'Shakira',
    id: 3
  },
]);

function addSlide() {

  const randomName = ref(faker.person.fullName());
  slides.value.push({
    title: randomName,
    id: slides.value.length + 1,
  })
}

const transitionTime = ref(15); // Default transition time in ms
const transitionEffect = ref("ease-in-out"); // Default transition effect

function updateOrder() {
  console.log("Updated order:", slides); // Logs new order for debugging
}
</script>

<template>
  <h1>Kiosk Carousel</h1>

  <div class="controls">
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
        <SlideThumb :title="element.title"></SlideThumb>
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