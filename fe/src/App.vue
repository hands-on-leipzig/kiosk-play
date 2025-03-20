<script setup>
import {provide} from "vue";
import {addListener, registerClient, slides} from "./components/websocket/SlideSocket.ts";

provide('websocket', {addListener, registerClient, slides});
</script>

<template>
  <RouterView></RouterView>
</template>

<style scoped>

</style>
