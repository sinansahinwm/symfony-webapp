// Mandatory Imports
import functions from '@google-cloud/functions-framework';

// Library Imports
import puppeteer from "puppeteer";
import {v4 as uuidv4} from 'uuid';
import axios from 'axios';
import {PuppeteerRunnerExtension} from "@puppeteer/replay";

// Define Functions
functions.http('puppeteerReplayer', _puppeteerReplayer);

// Define Function Handlers
function _puppeteerReplayer(request, response) {
    response.send("Hello from pup");
}