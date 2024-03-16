// Mandatory Imports
import functions from '@google-cloud/functions-framework';

// Library Imports
import {createRunner} from '@puppeteer/replay';
import puppeteer from "puppeteer";
import dotenv from 'dotenv';
import axios from 'axios';

// Vendor Imports
import PuppeteerBridgeExtension from './vendor/extension.js';

// Define Functions
functions.http('httpCloudFunction', _httpCloudFunctionHandler);

// Define Function Handlers
async function _httpCloudFunctionHandler(request, response) {

    const myHandlerFunction = request.get("X-Cloud-Function-Handler");

    switch (myHandlerFunction) {
        case "puppeteer_replayer":
            await _puppeteerReplayerHandler(request, response);
            break;
        default:
            response.status(500).send();
    }

}

async function _puppeteerReplayerHandler(request, response) {

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

    // Mandatory Data
    const requestBody = request.body;
    const webhookURL = requestBody.webhookURL;
    const instanceID = requestBody.instanceID;

    try {

        // Get Params
        const timeOut = requestBody.timeOut;
        const puppeteerLaunchOptions = requestBody.puppeteerLaunchOptions;
        const puppeteerSteps = requestBody.steps;

        // Create Puppeteer Instance
        const myBrowser = await puppeteer.launch({
            args: ['--no-sandbox', '--disable-setuid--sandbox'],
            ...puppeteerLaunchOptions,
        });
        const myPage = await myBrowser.newPage();

        // Create Flow
        const myFlow = {
            title: instanceID,
            steps: puppeteerSteps,
        };

        // Create Runner
        const myRunner = await createRunner(
            myFlow,
            new PuppeteerBridgeExtension(myBrowser, myPage, timeOut, webhookURL, instanceID, authHeaderName, validAppSecret)
        );

        // Run & Dispose
        await myRunner.run();
        await myBrowser.close();

    } catch (e) {

        // Runtime Errors Reporting
        const errorHookHeaders = {
            'Content-Type': 'application/json',
        }
        errorHookHeaders[authHeaderName] = validAppSecret;
        const errorHookData = {
            instanceID: instanceID,
            phase: "error",
            error: e.toString()
        }
        await axios.post(webhookURL, errorHookData, {headers: errorHookHeaders}).catch(function (error) {
            console.log(error);
        });

    }
}