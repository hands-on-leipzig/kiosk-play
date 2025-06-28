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
  <div class="plan-editor">
    <h2>Plan bearbeiten: {{ plan.name }}</h2>

    <form @submit.prevent="savePlanName">
      <input v-model="plan.name" placeholder="Planname" />
      <button type="submit">Speichern</button>
    </form>

    <div>
      <h3>Parameter</h3>
      <label v-for="(val, key) in params.values" :key="key">
        {{ key }}
        <input v-model="params.values[key]" />
      </label>
      <button @click="updateParams">Parameter speichern</button>
    </div>

    <div>
      <h3>Aktionen</h3>
      <button @click="generatePlan">Zeitplan generieren</button>
    </div>

    <div>
      <h3>Vorschau</h3>
      <iframe :src="previewUrl" width="100%" height="600"></iframe>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PlanEditor',
  props: {
    planId: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      plan: {},
      params: {
        values: {}
      },
      previewUrl: ''
    };
  },
  methods: {
    async loadData() {
      const res = await fetch(`/api/plan.load.php?id=${this.planId}`);
      const data = await res.json();
      this.plan = data.plan;
      this.params = data.params;
      this.previewUrl = `/output/zeitplan.cgi?plan=${this.planId}`;
    },
    async savePlanName() {
      await fetch('/api/plan.save.php', {
        method: 'POST',
        body: JSON.stringify({
          id: this.planId,
          name: this.plan.name
        }),
        headers: { 'Content-Type': 'application/json' }
      });
    },
    async updateParams() {
      await fetch('/api/plan.update_params.php', {
        method: 'POST',
        body: JSON.stringify({
          id: this.planId,
          values: this.params.values
        }),
        headers: { 'Content-Type': 'application/json' }
      });
    },
    async generatePlan() {
      await fetch('/api/plan.generate.php', {
        method: 'POST',
        body: JSON.stringify({ id: this.planId }),
        headers: { 'Content-Type': 'application/json' }
      });
      // Reload preview
      this.previewUrl = `/output/zeitplan.cgi?plan=${this.planId}&t=${Date.now()}`;
    }
  },
  mounted() {
    this.loadData();
  }
};
</script>

<style scoped>
.plan-editor {
  padding: 1rem;
}
</style>
