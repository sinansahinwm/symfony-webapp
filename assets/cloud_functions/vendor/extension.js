import {PuppeteerRunnerExtension} from "@puppeteer/replay";
import axios from 'axios';

export default class PuppeteerBridgeExtension extends PuppeteerRunnerExtension {

    constructor(browser, page, timeout, webhookUrl, instanceID, authHeader, authSecret) {
        super(browser, page, timeout);
        this.browser = browser;
        this.page = page;
        this.timeout = timeout;
        this.webhookUrl = webhookUrl;
        this.instanceID = instanceID;
        this.authHeader = authHeader;
        this.authSecret = authSecret;
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
        const authHeaderName = this.authHeader;
        const authHeaderValue = this.authSecret;

        const hookHeaders = {
            'Content-Type': 'application/json',
        }

        hookHeaders[authHeaderName] = authHeaderValue;

        await axios.post(this.webhookUrl, webhookData, {headers: hookHeaders})
            .catch(function (error) {
                console.log(error);
            });

    }

}