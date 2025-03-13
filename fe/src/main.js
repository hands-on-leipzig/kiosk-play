import Auth from "./components/Auth.vue";

const KEYCLOAK_URL = "https://sso.hands-on-technology.org";
const REALM = "master";
const CLIENT_ID = "kiosk";
const REDIRECT_URI = encodeURIComponent("https://kiosk.hands-on-technology.org/auth");

const redirectToKeycloak = () => {
    window.location.href = `${KEYCLOAK_URL}/realms/${REALM}/protocol/openid-connect/auth?client_id=${CLIENT_ID}&response_type=code&redirect_uri=${REDIRECT_URI}`;
};

// Check if user is logged in
const checkAuth = () => {
    const token = localStorage.getItem("jwt_token");
    if (!token) {
        redirectToKeycloak();
    }
};

checkAuth();

import './assets/main.css'

import {createApp} from 'vue'
import App from './App.vue'
import VueSplide from "@splidejs/vue-splide";
import {createMemoryHistory, createRouter} from 'vue-router'
import Carousel from "./components/Carousel.vue";
import CarouselSetup from "./components/CarouselSetup.vue";

import {library} from '@fortawesome/fontawesome-svg-core';
import {faEarth, faImage, faPlusCircle, faTrashCan} from '@fortawesome/free-solid-svg-icons';
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';

library.add(faPlusCircle, faTrashCan, faImage, faEarth);

const routes = [
    {path: '/', component: CarouselSetup},
    {path: '/carousel', component: Carousel},
    {path: '/auth', component: Auth},
]

const router = createRouter({
    history: createMemoryHistory(),
    routes,
})

const app = createApp(App)
app.use(VueSplide)
app.use(router)
app.component('fa', FontAwesomeIcon);
app.mount('#app')
