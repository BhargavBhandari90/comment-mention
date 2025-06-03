describe('Plugin UI', () => {
  it('Should load plugin\'s setting page', async () => {
    await page.goto('http://localhost:8888/wordpress/wp-login.php');

    await page.type('#user_login', 'admin');
    await page.type('#user_pass', 'admin');

    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.click('#wp-submit'),
    ]);

    // Optional: verify login worked
    await expect(page).toMatch('Dashboard');

    // Go to plugin settings
    await page.goto('http://localhost:8888/wordpress/wp-admin/admin.php?page=comment-mention');

    // Wait for heading or known element to ensure page loaded
    await expect(page).toMatch('Comment Mention Settings');
  });
});
