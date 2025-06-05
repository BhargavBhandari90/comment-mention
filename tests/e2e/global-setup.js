const { chromium, expect } = require('@playwright/test');


const fs = require('fs');
const { admin } = require('./config');

module.exports = async (config) => {
    const { stateDir, baseURL, userAgent } = config.projects[0].use;

    console.log(`State Dir: ${stateDir}`);
    console.log(`Base URL: ${baseURL}`);

    // used throughout tests for authentication
    process.env.ADMINSTATE = `${stateDir}adminState.json`;

    // Clear out the previous save states
    try {
        fs.unlinkSync(process.env.ADMINSTATE);
        console.log('Admin state file deleted successfully.');
    } catch (err) {
        if (err.code === 'ENOENT') {
            console.log('Admin state file does not exist.');
        } else {
            console.log('Admin state file could not be deleted: ' + err);
        }
    }

    let adminLoggedIn = false;

    const contextOptions = { baseURL, userAgent };

    // Create browser, browserContext, and page for customer and admin users
    const browser = await chromium.launch();
    const adminContext = await browser.newContext(contextOptions);
    const adminPage = await adminContext.newPage();

    const adminRetries = 5;
    for (let i = 0; i < adminRetries; i++) {
        try {
            console.log('Login as admin...');
            await adminPage.goto(`/wp-admin`);
            await adminPage.fill('input[name="log"]', admin.username);
            await adminPage.fill('input[name="pwd"]', admin.password);
            await adminPage.locator("#wp-submit").click();
            await adminPage.waitForLoadState('networkidle');
            await adminPage.goto(`/wp-admin`);
            await adminPage.waitForLoadState('domcontentloaded');

            await expect(adminPage.locator('div.wrap > h1')).toHaveText(
                'Dashboard'
            );
            await adminPage
                .context()
                .storageState({ path: process.env.ADMINSTATE });
            console.log('Logged-in as admin successfully.');
            adminLoggedIn = true;
            break;
        } catch (e) {
            console.log(
                `Admin login failed, Re-trying... ${i}/${adminRetries}`
            );
            console.log(e);
        }
    }

    if (!adminLoggedIn) {
        console.error(
            'Admin login failed... Check your credentials.'
        );
        process.exit(1);
    }

    await adminContext.close();
    await browser.close();
};
