import Auth from "./components/Auth.vue";
import './assets/main.css'

import {createApp} from 'vue'
import App from './App.vue'
import VueSplide from "@splidejs/vue-splide";
import {createRouter, createWebHistory} from 'vue-router'
import Carousel from "./components/Carousel.vue";
import CarouselSetup from "./components/CarouselSetup.vue";

import {library} from '@fortawesome/fontawesome-svg-core';
import {faEarth, faImage, faPlusCircle, faTrashCan} from '@fortawesome/free-solid-svg-icons';
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';

library.add(faPlusCircle, faTrashCan, faImage, faEarth);

const routes = [
    {path: '/setup', component: CarouselSetup},
    {path: '/carousel', component: Carousel},
    {path: '/auth', component: Auth},
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

const app = createApp(App)
app.use(VueSplide)
app.use(router)
app.component('fa', FontAwesomeIcon);
app.mount('#app')
