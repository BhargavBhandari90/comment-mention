const { test, expect } = require( '@playwright/test' );

test.use( { storageState: process.env.ADMINSTATE } );

test( 'Mention users in Post Comment', async ( { page } ) => {
    await page.goto('/hello-world');
    await page.getByRole('textbox', { name: 'Comment *' }).click();
    await page.getByRole('textbox', { name: 'Comment *' }).pressSequentially('@test');

    const mentionItem = page.locator('.tribute-container li.highlight');

    // Wait for the mention suggestion to appear
    await expect(mentionItem).toBeVisible({ timeout: 10000 });
    await expect(mentionItem).toContainText('testuser');

    await mentionItem.click();

    // await page.getByText('testuser (testuser)').click();
    await page.getByRole('textbox', { name: 'Comment *' }).pressSequentially(' this is test comment');
    await page.getByRole('button', { name: 'Post Comment' }).click();
    await expect(page.getByText('@testuser this is test comment').last()).toBeVisible({ timeout: 10000 });
});
