const { execSync } = require('child_process');

describe('Test User Mention in Post Comment', () => {

  beforeAll(() => {
    try {
      console.log('Creating test user...');
      execSync(
        'npx wp-env run cli wp user create testuser testuser@example.com --role=subscriber --user_pass=password',
        { stdio: 'inherit' }
      );
    } catch (err) {
      console.log('User may already exist. Skipping creation....');
    }
  });

  it('Should load plugin\'s setting page', async () => {

    await page.goto('http://localhost:8888/wp-login.php');

    await page.type('#user_login', 'admin');
    await page.type('#user_pass', 'password');

    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.click('#wp-submit'),
    ]);

    await expect(page).toMatch('Dashboard');

    await page.goto('http://localhost:8888/wp-admin/admin.php?page=comment-mention');

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
  });

  it('Should mention testuser in post comment', async () => {

    await page.goto('http://localhost:8888/hello-world/');

    await expect(page).toMatch('Hello world!');

    await page.type('textarea#comment', '@test');

    await page.waitForTimeout(5000);

    await page.waitForSelector('.tribute-container li.highlight', { visible: true });

    await page.click('.tribute-container li.highlight:first-child');

    await page.type('textarea#comment', ' this is a test comment');

    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      page.click('input#submit'),
    ]);

    await expect(page).toMatch('@testuser this is a test comment');

  });
});
