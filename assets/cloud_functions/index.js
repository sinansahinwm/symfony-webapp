import functions from '@google-cloud/functions-framework';

// Puppeteer Replayer
functions.http('puppeteerReplayer', _puppeteerReplayerHandler);

function _puppeteerReplayerHandler(request, response) {
    response.send("Hello from pup");
}