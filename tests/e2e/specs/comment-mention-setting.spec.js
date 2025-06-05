const { test, expect } = require( '@playwright/test' );

test.use( { storageState: process.env.ADMINSTATE } );

test( 'Check Plugin Setting Page', async ( { page } ) => {
    await page.goto('/wp-admin/');
    await page.getByRole('link', { name: 'Comment Mention' }).click();
    await expect(page.getByRole('heading', { name: 'Comment Mention' })).toBeVisible();
    await page.getByRole('cell', { name: 'Choose which user roles' }).getByLabel('Subscriber').check();
    await page.locator('input[name="cmt_mntn_email_enable"]').check();
    await page.locator('#submit').click();
    await expect(page.getByText('Settings Saved')).toBeVisible();
});
