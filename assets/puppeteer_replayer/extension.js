import {PuppeteerRunnerExtension} from "@puppeteer/replay";
import puppeteer from "puppeteer";
import {v4 as uuidv4} from 'uuid';
import axios from 'axios';

export default class PuppeteerBridgeExtension extends PuppeteerRunnerExtension {

    constructor(browser, page, timeout, webhookUrl, instanceID) {
        super(browser, page, timeout);
        this.browser = browser;
        this.page = page;
        this.timeout = timeout;
        this.instanceID = uuidv4();
        this.webhookUrl = webhookUrl;
        this.instanceID = instanceID;
    }

    async beforeAllSteps(flow) {
        await super.beforeAllSteps(flow);
        await this.sendWebhook('beforeAllSteps');
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

        // Get Data
        const pageContent = await this.page.content();
        const pageScreenshot = await this.page.screenshot({encoding: "base64"});

        // Add Snapshot
        const webhookData = {
            instanceID: this.instanceID,
            phase: phase,
            step: step,
            screenshot: pageScreenshot,
            content: pageContent,
        }

        // Send Webhook
        await axios.post(this.webhookUrl, webhookData);

    }

}