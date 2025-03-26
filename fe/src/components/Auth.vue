<script setup>
import {onMounted} from "vue";
import axios from "axios";
import {useRouter} from "vue-router";

const router = useRouter();

onMounted(async () => {
  const urlParams = new URLSearchParams(window.location.search);
  const code = urlParams.get("code");

  if (code) {
    try {
      let formData = new FormData()
      formData.set("code", code)
      const response = await axios.post("/api/auth", formData);
      console.log(response)
      localStorage.setItem("jwt_token", response.data.jwt_token);
      await router.push("/setup"); // Redirect to dashboard
    } catch (error) {
      console.error("Authentication failed", error);
    }
  }
});
</script>

<template>
  Authenticate ...
</template>

<style scoped>

</style>