<script setup>
import {UrlSlideContent} from "../../model/urlSlideContent.js";

const props = defineProps({
  content: UrlSlideContent,
});

</script>

<template>
    <object :data="props.content.url"></object>
</template>

<style scoped>
object {
  width: 100vw;
  height: 100vh;
  margin: 0;
}
</style>
