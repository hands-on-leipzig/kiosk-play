<script setup>
import { computed } from 'vue';
import ImageSlideContentRenderer from './ImageSlideContentRenderer.vue';
import RobotGameSlideContentRenderer from './RobotGameSlideContentRenderer.vue';
import {ImageSlideContent} from "../../model/imageSlideContent.js";
import {Slide} from "../../model/slide.js";
import {RobotGameSlideContent} from "../../model/robotGameSlideContent.js";

const props = defineProps({
  slide: Slide
});

const componentName = computed(() => {
  if (props.slide.content instanceof ImageSlideContent) {
    return ImageSlideContentRenderer;
  } else if (props.slide.content instanceof RobotGameSlideContent) {
    return RobotGameSlideContentRenderer;
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
