import Auth from "./components/Auth.vue"
import './assets/main.css'

import {createApp} from 'vue'
import App from './App.vue'
import VueSplide from "@splidejs/vue-splide";
import {createRouter, createWebHistory} from 'vue-router'
import Carousel from "./components/carousel/Carousel.vue";
import CarouselSetup from "./components/carousel/CarouselSetup.vue";

import {library} from '@fortawesome/fontawesome-svg-core';
import {faEarth, faImage, faPlusCircle, faTrashCan} from '@fortawesome/free-solid-svg-icons';
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';
import {sendMessage} from "./components/websocket/SlideSocket.js";
import ScheduleEditor from "./components/scheduler/ScheduleEditor.vue";

library.add(faPlusCircle, faTrashCan, faImage, faEarth);

const routes = [
    {path: '/setup', component: CarouselSetup},
    {path: '/carousel', component: Carousel},
    {path: '/auth', component: Auth},
    {path: '/schedule/:id', component: ScheduleEditor}
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

const app = createApp(App)

app.provide('sendMessage', sendMessage);
app.use(VueSplide)
app.use(router)
app.component('fa', FontAwesomeIcon);
app.mount('#app')
