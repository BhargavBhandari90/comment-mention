describe('Plugin UI', () => {
  it('Should load plugin\'s setting page', async () => {
    await page.goto('http://localhost:8888/wp-login.php');

    await page.type('#user_login', 'admin');
    await page.type('#user_pass', 'password');

    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.click('#wp-submit'),
    ]);

    // Optional: verify login worked
    await expect(page).toMatch('Dashboard');

    // Go to plugin settings
    await page.goto('http://localhost:8888/wp-admin/admin.php?page=comment-mention');

    // Wait for heading or known element to ensure page loaded
    await expect(page).toMatch('Comment Mention Settings');

    await page.click('input[name="cmt_mntn_email_enable"]');

    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      await page.click('#submit'),
    ]);

    await page.goto('http://localhost:8888/wp-admin/options-permalink.php');

    await page.click('#permalink-input-post-name');

    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      await page.click('#submit'),
    ]);


    await page.goto('http://localhost:8888/hello-world/');

    await expect(page).toMatch('Hello world!');

    await page.type('textarea#comment', '@adm');

    await page.waitForSelector('.tribute-container li.highlight', { visible: true });

    await page.click('.tribute-container li.highlight:first-child');

    await page.type('textarea#comment', ' this is a test comment');

    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.click('input#submit'),
    ]);

    // Optional: Confirm the comment appears on the page
    await expect(page).toMatch('@admin this is a test comment');

  });
});
