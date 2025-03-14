import {Slide} from "../../model/slide.js";
import {ImageSlideContent} from "../../model/imageSlideContent.js";
import {ref} from "vue";
import type {Ref} from "vue";

const listeners = [];
const websocket = new WebSocket('wss://echo.websocket.org');

export const slides: Ref = ref([]);

websocket.onopen = () => {
  console.log('Connected to websocket');
  // Register the client to the server
  // Here we should send some device information for the server to identify the client
  websocket.send(JSON.stringify({ type: 'register', clientId: navigator.userAgent }));
  setTimeout(() => {
    websocket.send(JSON.stringify({ type: 'setSlides', slides: [new Slide(1, 'Testbild', new ImageSlideContent('https://www.first-lego-league.org/files/relaunch2022/theme/layout/fll/logo/vertical/FIRSTLego_IconVert_RGB.png'))] }));
  }, 2000);
}

websocket.onmessage = event => {
  console.log('Received message:', event.data);
  if (event.data.startsWith('Request')) { // Special for echo.websocket.org, remove this
    return;
  }
  const msg = JSON.parse(event.data);
  handleEvent(msg);
  listeners.forEach(listener => listener(msg));
}

// TODO add error handling, reconnecting, etc

websocket.onerror = error => {
  console.error('WebSocket error:', error);
}

export function addListener(listener) {
  listeners.push(listener);
}

function handleEvent(event) {
  switch (event.type) {
    case 'setSlides':
        // Use custom deserialize function for slides
        slides.value = Slide.fromArray(event.slides);
        break;
  }
}
