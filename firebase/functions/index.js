// IMPORTS
const puppeteer = require('puppeteer');
const axios = require('axios');
const {onRequest} = require("firebase-functions/v2/https");
const {setGlobalOptions, logger} = require("firebase-functions/v2");

// CONFIGURATION
const scraperFunctionGlobalOptions = {
    memory: '4GiB',
    timeoutSeconds: 60,
    cpu: 4
}

const authorizationSecret = "8c9db0e6d88f9190ac9a001fadaf1e8d";
const puppeteerLaunchOptions = {
    headless: true,
    args: [
        '--no-sandbox',
        '--disable-setuid--sandbox',
        '--enable-chrome-browser-cloud-management',
        '--disable-features=site-per-process,',
        '--mute-audio'
    ],
};
const puppeteerOptions = {
    defaultUserAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36',
    defaultGeoLocation: {
        latitude: 39,
        longitude: 32,
    },
    waitUntil: 'networkidle0',
    timeout: 450000,
    viewPortWidth: 1366,
    viewPortHeight: 768,
    dataSaverMode: true,
    dataSaverModeBlockContents: ['stylesheet', 'image', 'media', 'font', 'eventsource', 'manifest', 'websocket', 'manifest', 'ping']
    // These are available content types
    // ('Document' | 'Stylesheet' | 'Image' | 'Media' | 'Font' | 'Script' | 'TextTrack' | 'XHR' | 'Fetch' | 'Prefetch' | 'EventSource' | 'WebSocket' | 'Manifest' | 'SignedExchange' | 'Ping' | 'CSPViolationReport' | 'Preflight' | 'Other');
};

// SETTING GLOBAL OPTIONS
setGlobalOptions(scraperFunctionGlobalOptions);

// FUNCTIONS

// Ping & Pong
exports.pingPong = onRequest((request, response) => {
    response.status(200).send();
});

// Firebase Scraper
exports.firebaseScraper = onRequest(async (request, response) => {

    // Authorization - Check Params Exist
    if ((!request.headers.authorization || !request.headers.authorization.startsWith('Bearer '))) {
        response.status(403).send('Unauthorized');
        return;
    }

    // Authorization - Check Token
    const bearerToken = request.headers.authorization.replaceAll("Bearer", "").replaceAll(" ", "");
    if (bearerToken !== authorizationSecret) {
        response.status(401).send('Unauthorized');
        return;
    }

    // Check - Method
    const requestMethod = request.method.toLowerCase();
    if (requestMethod !== "post") {
        response.status(405).send('Method Not Allowed');
        return;
    }

    // Check - Content Type
    const requestContentType = request.get('Content-Type');
    if (requestContentType !== "application/json") {
        response.status(400).send('Bad Request');
        return;
    }

    // Check - Mandatory Params
    const requestBody = request.body;
    const instanceID = requestBody.instanceID;
    const navigateURL = requestBody.navigateURL;
    const webhookURL = requestBody.webhookURL;
    if ((typeof navigateURL === "undefined") || (typeof webhookURL === "undefined")) {
        response.status(400).send("Bad Request");
        return;
    }

    // Check - URL Must HTTPS
    if (!(navigateURL.startsWith("https")) || !(navigateURL.startsWith("https"))) {
        response.status(400).send("HTTPS Required");
        return;
    }

    // Set Axios Auth Defaults
    axios.defaults.headers.post['Authorization'] = 'Bearer ' + authorizationSecret;
    axios.defaults.headers.post['Content-Type'] = 'application/json';

    // Try To Launch Puppeteer
    try {

        // Check Proccess Immediately
        const proccessImmediately = requestBody.proccessImmediately;

        if (proccessImmediately !== true) {

            // Create Axios Worker Request
            const workerRequestConfig = {
                ...requestBody,
                proccessImmediately: true
            }

            axios.post(requestBody.workerURL, workerRequestConfig).catch(function (error) {
                logger.error(error);
            });

            response.status(200).send("OK");
            return;
        }

        // Get Remote Launch Options
        const puppeteerLaunchOptionsRequested = requestBody.puppeteerLaunchOptions ?? {};

        // Open Browser
        const myBrowser = await puppeteer.launch({
            ...puppeteerLaunchOptions,
            ...puppeteerLaunchOptionsRequested
        }).catch((err) => {
            logger.error("Browser launch error." + err.toString());
        });

        // Create New Page
        const myPage = await myBrowser.newPage();

        // Set Page GeoLocation
        await myPage.setGeolocation(puppeteerOptions.defaultGeoLocation);

        // Set Page Viewport
        await myPage.setViewport({
            width: puppeteerOptions.viewPortWidth,
            height: puppeteerOptions.viewPortHeight,
            deviceScaleFactor: 1,
            isMobile: false,
            hasTouch: false,
            isLandscape: false
        });

        // Set Page User Agent
        await myPage.setUserAgent(puppeteerOptions.defaultUserAgent);

        // Activate Data Saver Mode If Needed
        if (puppeteerOptions.dataSaverMode === true) {

            // Set Request Interception
            await myPage.setRequestInterception(true);

            // Block to Load Other Assets
            myPage.on('request', (request) => {
                if (puppeteerOptions.dataSaverModeBlockContents.indexOf(request.resourceType()) !== -1) {
                    request.abort();
                } else {
                    request.continue();
                }
            });

        }


        // Navigate
        const myResponse = await myPage.goto(navigateURL, {
            waitUntil: puppeteerOptions.waitUntil,
            timeout: puppeteerOptions.timeout,
        });

        // Get Data
        const pageContent = await myPage.content();
        const pageScreenshot = (puppeteerOptions.dataSaverMode === true) ? '' : await myPage.screenshot({encoding: "base64"});
        const initialPageUrl = myPage.url();

        // Prepare Webhook Data
        const myWebhookPayload = {
            instanceID: instanceID,
            screenshot: pageScreenshot,
            content: Buffer.from(pageContent).toString('base64'),
            url: initialPageUrl,
            status: (myResponse !== null) ? myResponse.status() : 500,
        };

        // Send Webhook Post
        await axios({
            method: 'post',
            url: webhookURL,
            data: myWebhookPayload,
        }).catch(function (error) {
            logger.error(error);
        });

        // Dispose Browser
        await myBrowser.close();

        response.status(200).send("OK");

    } catch (e) {

        // Add Firebase Error & Return 500
        logger.error(e);
        response.status(500).send();

    }

});