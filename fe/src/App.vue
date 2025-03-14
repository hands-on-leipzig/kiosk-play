<script setup>
import {provide} from "vue";
import {addListener, slides} from "./components/websocket/SlideSocket.ts";

provide('websocket', {addListener, slides});
</script>

<template>
  <RouterView></RouterView>
</template>

<style scoped>

</style>
