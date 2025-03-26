<script setup>

import {ImageSlideContent} from "../model/imageSlideContent.js";
import {RobotGameSlideContent} from "../model/robotGameSlideContent.js";
import {UrlSlideContent} from "../model/urlSlideContent.js";

let types = {
  1: new ImageSlideContent(),
  2: new RobotGameSlideContent(),
  3: new UrlSlideContent()
}

</script>

<template>
<div>
  <select>
    <option v-for="type of types" :id="type" v-text="type.toJSON().type"></option>
  </select>
</div>
</template>

<style scoped>

</style>