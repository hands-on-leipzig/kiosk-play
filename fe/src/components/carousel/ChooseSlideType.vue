<script setup>

import {ImageSlideContent} from "../../model/imageSlideContent.ts";
import {RobotGameSlideContent} from "../../model/robotGameSlideContent.ts";
import {UrlSlideContent} from "../../model/urlSlideContent.ts";
import {ref} from "vue";
import api from "../../services/api.js";
import {Slide} from "../../model/slide.ts";

let types = {
  image: new ImageSlideContent("").toJSON().type,
  rg: new RobotGameSlideContent().toJSON().type,
  url: new UrlSlideContent("").toJSON().type
}

let slideType = ref("")
let url = ref("")

async function pushSlide() {
  let s
  switch (slideType.value) {
    case types.image:
      s = new Slide(1, "title", new ImageSlideContent(url.value))
      break;
    case types.rg:
      s = new Slide(1, "title", new RobotGameSlideContent())
      break;
    case types.url:
      s = new Slide(1, "title", new UrlSlideContent(url.value))
      break;
  }

  let d = new FormData
  d.set("title", s.title)
  d.set("content", JSON.stringify(s.content.toJSON()))
  await api.post("/api/events/1/slides", d)
  document.location.reload()
}

</script>

<template>
  <div>
    <form @submit.prevent="pushSlide">
      <select v-model="slideType">
        <option v-for="type of types" :id="type" v-text="type"></option>
      </select>
      <input v-model="url" placeholder="URL/PFAD" type="text">
      <input type="submit" value="Erstellen">
    </form>
  </div>
</template>

<style scoped>
div {
  position: fixed;
  width: 50vw;
  height: 50vh;
  background-color: grey;
  margin: auto auto;
}
</style>