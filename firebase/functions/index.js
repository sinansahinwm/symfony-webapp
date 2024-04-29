// Firebase Cloud Functions

// Firebase Imports
const {onRequest} = require("firebase-functions/v2/https");
const logger = require("firebase-functions/logger");
const {setGlobalOptions} = require("firebase-functions/v2");

// Puppeteer Replayer Imports
const puppeteer = require('puppeteer');
const {createRunner, PuppeteerRunnerExtension} = require("@puppeteer/replay");
const axios = require('axios');

// Set Global Options
const globalOptions = {timeoutSeconds: 300, memory: "4GiB"};
setGlobalOptions(globalOptions);

// [FUNCTION] : Ping & Pong
exports.pingPong = onRequest((request, response) => {
    response.status(200).send();
});

// [FUNCTION] : Puppeteer Replayer
exports.puppeterReplayer = onRequest(async (request, response) => {

    // Authorization & Webhook Configuration
    const puppeteerReplayerConfiguration = {
        authorizationHeader: "X-Authorization-AppSecret",
        authorizationSecret: "8c9db0e6d88f9190ac9a001fadaf1e8d",
        // DEPRECED allowedHooks: "beforeAllSteps,beforeEachStep,afterEachStep,afterAllSteps"
        allowedHooks: "afterEachStep,afterAllSteps",
        defaultUserAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
        defaultGeoLocation: {
            latitude: 39,
            longitude: 32,
        },
    };

    // Check For Authentication
    const requestAppSecret = request.get(puppeteerReplayerConfiguration.authorizationHeader);
    if (puppeteerReplayerConfiguration.authorizationSecret !== requestAppSecret) {
        response.status(401).send();
        return;
    }

    // Check For Content Type
    const requestContentType = request.get('Content-Type');
    if (requestContentType.toString() !== "application/json") {
        response.status(415).send();
        return;
    }

    // Check Mandatory Data
    try {
        const requestBody = request.body;
        const webhookURL = requestBody.webhookURL;
        const instanceID = requestBody.instanceID;
    } catch (e) {
        response.status(400).send();
        return;
    }


    // Get Data
    const requestBody = request.body;
    const webhookURL = requestBody.webhookURL;
    const instanceID = requestBody.instanceID;
    const userID = requestBody.userID;

    // Puppeteer Configuration
    const puppeteerLaunchOptions = {
        headless: true,
        args: [
            '--no-sandbox',
            '--disable-setuid--sandbox',
            '--enable-chrome-browser-cloud-management',
            '--disable-features=site-per-process,'
        ],
        // DEPRECED userDataDir: './tmp/user_' + userID,
    };

    // Try To Run
    try {

        // Send 200 Code If Page Opened
        response.status(200).send("OK");

        // Get Params
        const timeOut = requestBody.timeOut;
        const puppeteerLaunchOptionsRequested = requestBody.puppeteerLaunchOptions;
        const puppeteerSteps = requestBody.steps;

        // Open Browser
        const myBrowser = await puppeteer.launch({
            ...puppeteerLaunchOptions,
            ...puppeteerLaunchOptionsRequested
        });

        // Create New Page
        const myPage = await myBrowser.newPage();

        // Set Page GeoLocation
        await myPage.setGeolocation(puppeteerReplayerConfiguration.defaultGeoLocation);

        // Set Page User Agent
        await myPage.setUserAgent(puppeteerReplayerConfiguration.defaultUserAgent);

        // Create Flow
        const myFlow = {
            title: instanceID,
            steps: puppeteerSteps,
        };

        // Create Runner
        const myExtension = new PuppeteerBridgeExtension(
            myBrowser,
            myPage,
            timeOut,
            webhookURL,
            instanceID,
            puppeteerReplayerConfiguration.authorizationHeader,
            puppeteerReplayerConfiguration.authorizationSecret,
            puppeteerReplayerConfiguration.allowedHooks
        );
        const myRunner = await createRunner(
            myFlow,
            myExtension
        );

        // Run & Dispose
        await myRunner.run();
        await myBrowser.close();

    } catch (e) {

        // Add Firebase Error & Return 500
        logger.error(e);
        response.status(500).send();


        // Add Hook Error
        const errorHookHeaders = {
            'Content-Type': 'application/json',
        }
        errorHookHeaders[puppeteerReplayerConfiguration.authorizationHeader] = puppeteerReplayerConfiguration.authorizationSecret;
        const errorHookData = {
            instanceID: instanceID,
            phase: "error",
            error: e.toString()
        }
        await axios.post(webhookURL, errorHookData, {headers: errorHookHeaders}).catch(function (error) {
            logger.error(error);
        });

    }
});

// [HELPER] : Puppeteer Bridge Extension
class PuppeteerBridgeExtension extends PuppeteerRunnerExtension {
    constructor(browser, page, timeout, webhookUrl, instanceID, authHeader, authSecret, allowedHooks) {
        super(browser, page, timeout);
        this.browser = browser;
        this.page = page;
        this.timeout = timeout;
        this.webhookUrl = webhookUrl;
        this.instanceID = instanceID;
        this.authHeader = authHeader;
        this.authSecret = authSecret;
        this.allowedHooks = allowedHooks;

        // Constants
        this.playbackSpeed = 5000;
    }

    async beforeAllSteps(flow) {
        await super.beforeAllSteps(flow);
        await this.sendWebhook('beforeAllSteps');

        // Sleep Until Miliseconds
        await this.sleepUntilMS(this.playbackSpeed);

        // Check Captcha
        console.log("DEBUG : BEFORE ALL STEPS (Ex: Check Captcha)");
    }

    async beforeEachStep(step, flow) {
        await super.beforeEachStep(step, flow);
        await this.sendWebhook('beforeEachStep', step);
    }

    async afterEachStep(step, flow) {
        await super.afterEachStep(step, flow);
        await this.sendWebhook('afterEachStep', step);
    }

    async afterAllSteps(flow) {
        await super.afterAllSteps(flow);
        await this.sendWebhook('afterAllSteps');
    }

    async sendWebhook(phase = null, step = null) {

        if (phase !== null) {
            if (this.allowedHooks.includes(phase) === false) {
                return;
            }
        }

        // Get Data
        const pageContent = await this.page.content();
        const pageScreenshot = await this.page.screenshot({encoding: "base64"});
        const initialPageUrl = this.page.url()

        // Add Snapshot
        const webhookData = {
            instanceID: this.instanceID,
            phase: phase,
            step: step,
            screenshot: pageScreenshot,
            content: pageContent,
            url: initialPageUrl
        }

        // Send Webhook
        const authHeaderName = this.authHeader;
        const authHeaderValue = this.authSecret;

        const hookHeaders = {
            'Content-Type': 'application/json',
        }

        hookHeaders[authHeaderName] = authHeaderValue;

        await axios.post(this.webhookUrl, webhookData, {headers: hookHeaders})
            .catch(function (error) {
                logger.error(error);
            });

    }
    sleepUntilMS(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

}
