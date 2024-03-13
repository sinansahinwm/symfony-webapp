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
    const dotEnv = dotenv.config({ path: './../../.env' });
    console.log(dotEnv);

    // Get Request Parameters
    const requestAppSecret = request.headers["X-Authorization-AppSecret"];
    const validAppSecret = "8c9db0e6d88f9190ac9a001fadaf1e8d";
    const requestContentType = request.get('Content-Type').toLowerCase();

    // Check Content Type & Authorization
    if ((requestContentType === "application/json") && (requestAppSecret === validAppSecret)) {

        // Get Data
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

        response.send("OK");
    }

    response.next();
}