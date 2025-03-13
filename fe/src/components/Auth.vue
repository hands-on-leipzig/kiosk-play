<script setup>
import { onMounted } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

const router = useRouter();

onMounted(async () => {
  const urlParams = new URLSearchParams(window.location.search);
  const code = urlParams.get("code");

  if (code) {
    try {
      const response = await axios.post("be/api/auth.php", { code });
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