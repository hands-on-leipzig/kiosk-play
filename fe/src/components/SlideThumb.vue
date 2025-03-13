<script setup>
defineProps({
  title: String
});

function deleteSlide() {

}
</script>

<template>
  <div class="slide-thumb">
    <span class="button-row"><fa @click="deleteSlide()" :icon="['fas', 'trash-can']"></fa></span>
    <div class="thumb"></div>
    <span>{{ title }}</span>
  </div>
</template>

<style scoped>
.slide-thumb {
  display: flex;
  flex-direction: column;
  position: relative;
  background-color: cornflowerblue;
  width: 18rem;
  height: 11rem;
  margin: .5rem;
  cursor: grab;
}

.thumb {
  width: 16rem;
  height: 9rem;
  background-color: lightblue;
}

.slide-thumb > * {
  width: 100%;
}

.button-row {
  display: flex;
  justify-content: flex-end;
}

.button-row > * {
  margin: .2rem;
  cursor: pointer;
}
</style>