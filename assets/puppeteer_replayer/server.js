import express from 'express';

const myServer = express();
const myServerPort = 3000;

myServer.listen(myServerPort, () =>
    console.log('Puppeteer Replayer Server Started On Port: ' + myServerPort),
);

myServer.get('/', (req, res) => {
    res.send('Hello World!')
})