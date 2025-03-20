import { WebSocketServer } from 'ws';

const sockets = [];
let slides = [
    {
        id:1,
        title:"Testbild",
        content: {
            type: "ImageSlideContent",
            imageUrl:"https://www.first-lego-league.org/files/relaunch2022/theme/layout/fll/logo/vertical/FIRSTLego_IconVert_RGB.png"
        }
    }
];

const wss = new WebSocketServer({port: 3000});

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

    console.log("Sending slides", slides);
    ws.send(JSON.stringify({
        type: 'setSlides',
        slides: slides
    }));
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
            sendSlidesMatching((socket) => socket !== origin);
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

function sendSlides(socket) {
    console.log('Send slides:', slides);
    socket.send(JSON.stringify({
        type: 'setSlides',
        slides: slides
    }));
}
