=== Comment Mention ===
Contributors: bhargavbhandari90, biliplugins, hackkzy404
Donate link: https://www.paypal.me/BnB90/10
Tags: comments, mention, tagging, bbPress, WooCommerce
Requires at least: 4.6
Tested up to: 6.8.3
Stable tag: 1.7.20
Requires PHP: 5.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Mention users in WordPress comments without needing BuddyPress! Automatically notify mentioned users via email. Also supports bbPress.

== Description ==

**Comment Mention** lets you easily enable @mentions in your WordPress comments — no BuddyPress required!

When someone mentions a user by typing `@username` in a comment, the mentioned user receives an email notification. Perfect for blogs, communities, and discussion-based sites.

**Key features:**

- Simple and lightweight — works with the native WordPress comment system.
- Mention users by typing `@username`.
- Sends customizable email notifications to mentioned users.
- Backend settings to customize email subject and content.
- Compatible with bbPress — mention users in forum topics and replies.
- Compatible with WooCommerce - mention users in product reviews.

== FREE Plugin Compatibility ==

- bbPress - Mention users in topics & replies.
- WooCommerce - Mention users in product reviews.

**Watch it in action:**

https://www.youtube.com/watch?v=Nz47aKJhsKQ

---

== PRO Features ==

Upgrade to **Comment Mention Pro** to unlock advanced features:

- Autocomplete search by first name, last name, or display name while mentioning.
- Enable mentions in page comments.
- Option to disable email notifications for mentions.
- Future: Mention by first & last name (under development).
- Extra controls for role-based mention restrictions.
- Additional hooks and filters for developers.

🛠️ Go to **wp-admin → Comment Mention** to enable pro features.  
🔗 [Get Comment Mention Pro](https://biliplugins.com/comment-mention-pro-product/)

> **Note:** Requires Comment Mention plugin v1.3.0 or higher.

---

== Pro Plugin Compatibility ==

- [GamiPress](https://gamipress.com/?ref=203) — reward users with points, achievements, or ranks when they mention or get mentioned.
- [MemberPress](https://memberpress.com/) — integrate mentions into membership sites.

---

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/comment-mention` directory, or install via the WordPress plugins screen directly.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Configure settings under **Comment Mention** in the admin menu.

---

== Screenshots ==

1. Mention the user by typing `@` in the comment box.
2. Admin settings screen to customize email notifications.

---

== GitHub Repository ==

[https://github.com/BhargavBhandari90/comment-mention](https://github.com/BhargavBhandari90/comment-mention)

---

== Changelog ==

= 1.7.20 =
* Added hook for email content.

= 1.7.19 =
* Fixed - Mentioning users in bbPress topics and replies when bbp style pack is active.

= 1.7.18 =
* Improvements for PHP 8.x.
* Fixed - Displaying span tag in bbPress replies.

= 1.7.17 =
* Fixed - Pro links displaying even when pro plugin was active.

= 1.7.16 =
* Removed case sensitivity when searching for users.

= 1.7.15 =
* Added option to enable avatars for mentioned users.

= 1.7.14 =
* Improved mention script for pro plugin.
* Added hooks for pro plugin.

= 1.7.13 =
* Further improvements to mention script for pro plugin.

= 1.7.12 =
* Mention script optimization.

= 1.7.11 =
* Added `cmt_mntn_comment_pre_content` hook.
* Updated mention script for pro features.

= 1.7.10 =
* Fixed bbPress reply link.

= 1.7.9 =
* Bug fix: Double quotes issue with email subject.

= 1.7.8 =
* Added hook: `cmnt_mntn_replace_mentioned_name`.

= 1.7.7 =
* Bug fix: Unable to mention users on freshly installed Comment Mention.

= 1.7.6 =
* Fixed plugin update issue.

= 1.7.5 =
* Minor bug fix.

= 1.7.4 =
* Minor bug fix.

= 1.7.3 =
* Added shortcode support for email subject.

= 1.7.2 =
* Minor updates.

= 1.7.1 =
* Minor bug fixes.

= 1.7.0 =
* Updated mention script.
* Minor bug fixes.

= 1.6.0 =
* Added option to hide selected user roles when mentioning.

= 1.5.0 =
* Added option to enable mentions for selected user roles.

= 1.4.6 =
* JS & CSS optimization.

= 1.4.5 =
* JS & CSS optimization.

= 1.4.4 =
* Fixed: Not able to mention.

= 1.4.3 =
* Fixed: Not able to mention.

= 1.4.2 =
* Adjustments for pro plugin.

= 1.4.1 =
* Adjustments for pro plugin.

= 1.4.0 =
* Added TinyMCE support.

= 1.3.3 =
* Minor bug fixes.

= 1.3.2 =
* Updates for pro plugin.

= 1.3.1 =
* Updates for pro plugin.

= 1.3.0 =
* Updates for pro plugin.

= 1.2.5 =
* Added actions and filters.

= 1.2.4 =
* Added placeholders: #commenter_name#, #comment_content#.

= 1.2.3 =
* Fixed AJAX issue with WP 5.5.

= 1.2.2 =
* Fixed class issue for non-admin mentions.

= 1.2.1 =
* Fixed mention issue with post comments.

= 1.2.0 =
* Added bbPress compatibility.

= 1.1.0 =
* Added language support.

= 1.0.1 =
* Fixed double email issue for mentioned users.

= 1.0.0 =
* Initial release.

---

== Upgrade Notice ==

= 1.7.19 =
Recommended update to fix bbPress compatibility and improve stability.
