<script setup>
import {ImageSlideContent} from '../../../model/imageSlideContent.ts';

const props = defineProps({
  content: ImageSlideContent
});
</script>

<template>
  <div class="slide-container">
    <img class="slide-image" :src="content.imageUrl">
  </div>
</template>

<style scoped>
.slide-image {
  object-fit: contain;
  max-width: 100%;
  max-height: 100%;
}

.slide-container {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  height: 100%;
  position: relative;
}
</style>
