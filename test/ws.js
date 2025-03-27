import { WebSocketServer } from 'ws';

const sockets = [];
let slides = [];

let index = 0;
let duration = 10 * 1000;

const wss = new WebSocketServer({port: 3000});

setInterval(() => {
    let newIndex = index + 1;
    if (index >= slides.length) {
        newIndex = 0;
    }

    console.log("new index", newIndex);

    if (slides.length === 0) {
        return;
    }

    index = newIndex;
    const currentSlide = slides[newIndex];
    console.log("new slide", currentSlide);

    sendMessage(JSON.stringify({
        type: 'setCurrentSlide',
        slide: currentSlide
    }));
}, duration);

wss.on('connection', (ws) => {
    console.log('Connection established');
    sockets.push(ws);

    ws.on('message', (message) => {
        handleMessage(message, ws);
    });

    ws.on('close', () => {
        console.log('Connection closed');
        const index = sockets.indexOf(ws);
        sockets.splice(index, 1);
    });

    /*ws.send(JSON.stringify({
        type: 'setSlides',
        slides: slides
    }));*/
})


function handleMessage(message, origin) {
    message = JSON.parse(message);
    console.log(message);
    switch (message.type) {
        case 'register':
            console.log("Register:", message.clientId);
            break;
        case 'saveSlides':
            slides = message.slides;
            console.log("saved slides", slides);
            // sendSlidesMatching((socket) => socket !== origin);
    }
}

function sendSlidesMatching(shouldSend) {
    for (const socket of sockets) {
        if (!shouldSend(socket)) {
            continue;
        }
        sendSlides(socket)
    }
}

function sendMessage(message) {
    console.log("Sending message to all clients: ", message);
    for (const socket of sockets) {
        socket.send(message);
    }
}

function sendSlides(socket) {
    console.log('Send slides:', slides);
    socket.send(JSON.stringify({
        type: 'setSlides',
        slides: slides
    }));
}
