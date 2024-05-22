/*
    ---- IMPORTS ----
*/
const {onRequest} = require("firebase-functions/v2/https");

/*
    ---- FUNCTIONS ----
*/

// [ FUNCTION ]: Ping & Pong
exports.pingPong = onRequest((request, response) => {
    response.status(200).send();
});

// [ FUNCTION ]: Dom Content Crawler
exports.domContentCrawler = onRequest(async (request, response) => {
    response.send("sdgsdg");
});