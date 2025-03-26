<script setup>
import {UrlSlideContent} from "../../model/urlSlideContent.js";

const props = defineProps({
  slide: UrlSlideContent,
});
</script>

<template>
    <object :data="props.slide.url"></object>
</template>

<style scoped>

</style>
