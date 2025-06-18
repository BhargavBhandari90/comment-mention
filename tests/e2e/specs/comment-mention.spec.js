const { test, expect } = require( '@playwright/test' );

test.use( { storageState: process.env.ADMINSTATE } );

test( 'Mention users in Post Comment', async ( { page } ) => {
    await page.goto('/hello-world');

    await page.getByRole('textbox', { name: 'Comment *' }).click();
    await page.getByRole('textbox', { name: 'Comment *' }).pressSequentially('@test',{ delay: 200 });

    // Wait for the mention suggestion to appear.
    const mentionItem = page.locator('.tribute-container li.highlight');
    await expect(mentionItem).toBeVisible({ timeout: 3000 });
    await expect(mentionItem).toContainText('testuser');

    await mentionItem.click();

    await page.getByRole('textbox', { name: 'Comment *' }).pressSequentially(' this is test comment',{ delay: 100 });
    await page.getByRole('button', { name: 'Post Comment' }).click();
    await expect(page.getByText('@testuser this is test comment').last()).toBeVisible({ timeout: 10000 });
});
