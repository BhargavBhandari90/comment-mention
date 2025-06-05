const { test, expect } = require( '@playwright/test' );

test.use( { storageState: process.env.TESTUSERSTATE } );

test( 'Mention users in Post Comment by Subscriber', async ( { page } ) => {
    await page.goto('/hello-world');
    await page.getByRole('textbox', { name: 'Comment *' }).click();
    await page.getByRole('textbox', { name: 'Comment *' }).pressSequentially('@ad',{ delay: 200 });

    const mentionItem = page.locator('.tribute-container li.highlight');

    // Wait for the mention suggestion to appear
    await expect(mentionItem).toBeVisible({ timeout: 3000 });
    await expect(mentionItem).toContainText('admin');

    await mentionItem.click();

    // await page.getByText('testuser (testuser)').click();
    await page.getByRole('textbox', { name: 'Comment *' }).pressSequentially(' this is test comment',{ delay: 100 });
    await page.getByRole('button', { name: 'Post Comment' }).click();
    await expect(page.getByText('@admin this is test comment').last()).toBeVisible({ timeout: 10000 });
});
