<script setup>
//import {UrlSlideContent} from "../../model/urlSlideContent.js";

import {UrlSlideContent} from "../../model/urlSlideContent.js";

const props = defineProps({
  slide: UrlSlideContent,
});
</script>

<template>
    <object :data="slide.url"></object>
</template>

<style scoped>

</style>
