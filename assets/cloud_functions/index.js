// Mandatory Imports
import functions from '@google-cloud/functions-framework';

// Library Imports
import {createRunner} from '@puppeteer/replay';
import puppeteer from "puppeteer";
import dotenv from 'dotenv';

// Vendor Imports
import PuppeteerBridgeExtension from './vendor/extension.js';

// Define Functions
functions.http('puppeteerReplayer', _puppeteerReplayer);

// Define Function Handlers
async function _puppeteerReplayer(request, response) {

    // Read DotEnv
    const envPath = import.meta.dirname + "/../../.env";
    const dotEnv = dotenv.config({path: envPath});
    const parsedDotEnv = dotEnv.parsed;

    // Get Request Parameters
    const authHeaderName = parsedDotEnv.CLOUD_FUNCTIONS_AUTHORIZATION_HEADER;
    const validAppSecret = parsedDotEnv.APP_SECRET;
    const requestAppSecret = request.get(authHeaderName);

    // Check For Authentication
    if (validAppSecret !== requestAppSecret) {
        response.status(401).send();
        return;
    }

    // Check For Content Type
    const requestContentType = request.get('Content-Type');
    if (requestContentType.toString() !== "application/json") {
        response.status(415).send();
        return;
    }

    // Send 200 Status Code & Run Steps
    response.status(200).send("OK");

    try {

        // Get Params
        const requestBody = request.body;
        const instanceID = requestBody.instanceID;
        const webhookURL = requestBody.webhookURL;
        const timeOut = requestBody.timeOut;
        const puppeteerLaunchOptions = requestBody.puppeteerLaunchOptions;
        const puppeteerSteps = requestBody.steps;

        // Create Puppeteer Instance
        const myBrowser = await puppeteer.launch(puppeteerLaunchOptions);
        const myPage = await myBrowser.newPage();

        // Create Flow
        const myFlow = {
            title: instanceID,
            steps: puppeteerSteps,
        };

        // Create Runner
        const myRunner = await createRunner(
            myFlow,
            new PuppeteerBridgeExtension(myBrowser, myPage, timeOut, webhookURL, instanceID)
        );

        // Run & Dispose
        await myRunner.run();
        await myBrowser.close();

    } catch (e) {
        // TODO : Error handler
    }

}