import express from 'express';

const myServer = express();
const myServerPort = 3030;

myServer.listen(myServerPort, () =>
    console.log('Puppeteer Replayer Server Started On Port: ' + myServerPort),
);

myServer.get('/', (req, res) => {
    res.send('Hello World!')
})