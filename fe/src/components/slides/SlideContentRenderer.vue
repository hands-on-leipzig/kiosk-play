<script setup>
import { defineProps, computed } from 'vue';
import ImageSlideContentRenderer from './ImageSlideContentRenderer.vue';
import {ImageSlideContent} from "../../model/imageSlideContent.js";
import {Slide} from "../../model/slide.js";

const props = defineProps({
  slide: Slide
});

const componentName = computed(() => {
  if (props.slide.content instanceof ImageSlideContent) {
    return ImageSlideContentRenderer;
  }
  // TODO: Add renderers for other subtypes (RobotGameScore, FlowView, etc)
  return null;
});
</script>

<template>
  <div class="slide-content">
    <component :is="componentName" :content="slide.content"></component>
  </div>
</template>

<style scoped>
.slide-content {
  height: 100%;
  overflow: hidden;
}
</style>
