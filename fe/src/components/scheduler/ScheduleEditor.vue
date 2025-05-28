<script setup>
import {computed} from "vue";
import { useRoute } from 'vue-router'

const id = computed(() => parseInt(useRoute().params.id, 10))
</script>

<template>
  <div v-text="id"></div>
<object></object>
</template>

<style scoped>

</style>