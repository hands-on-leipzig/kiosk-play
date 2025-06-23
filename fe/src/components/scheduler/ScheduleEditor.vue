<script setup>
import {computed, ref} from "vue";
import { useRoute } from 'vue-router'
import api from "../../services/api.js";

const KEYCLOAK_URL = "https://sso.hands-on-technology.org";
const REALM = "master";
const CLIENT_ID = "kiosk";
const REDIRECT_URI = encodeURIComponent("https://kiosk.hands-on-technology.org/auth");

const redirectToKeycloak = () => {
  window.location.href = `${KEYCLOAK_URL}/realms/${REALM}/protocol/openid-connect/auth?client_id=${CLIENT_ID}&response_type=code&redirect_uri=${REDIRECT_URI}`;
};

let newSlide = ref(false)

function isValidJwt(token) {
  if (!token) return false;

  try {
    // Split the token into parts
    const payloadBase64 = token.split('.')[1];
    if (!payloadBase64) return false;

    // Decode the payload
    const payload = JSON.parse(atob(payloadBase64));

    // Check for expiration timestamp (exp)
    const expiration = payload.exp * 1000; // Convert to milliseconds
    const now = Date.now();

    return expiration > now;
  } catch (e) {
    console.error("Invalid JWT token:", e.message);
    return false;
  }
}

// Check if user is logged in
const checkAuth = () => {
  if (document.location.host === "localhost:5173") return true;
  const token = localStorage.getItem("jwt_token");
  if (!token || !isValidJwt(token)) {
    redirectToKeycloak();
  }
};

checkAuth();

const id = computed(() => parseInt(useRoute().params.id, 10))

async function fetchSchedule() {
  try {
    const response = await api.get('/api/scheduler/schedule/' + id)
    if (response && response.data) {
      Object.keys(schedule).forEach((key) => {
        schedule[key] = response.data[key]
      })
    }
  } catch (error) {
    console.log("Error fetching settings: ", error.message)
  }
}

</script>

<template>
  <div v-text="id"></div>
<object></object>
</template>

<style scoped>

</style>