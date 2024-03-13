// Add Imports
import express from 'express';
import 'dotenv/config'
import {configDotenv} from "dotenv";

// Define App
const dotEnv = configDotenv({path: "../../.env"}).parsed;
const myServer = express();
const myServerPort = dotEnv.NODE_SERVER_PORT;

myServer.listen(myServerPort, () =>
    console.log('Node Server Started On Port: ' + myServerPort),
);

myServer.get('/', (req, res) => {
    res.send('Hello World!');
});