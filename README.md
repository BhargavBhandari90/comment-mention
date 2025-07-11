# Comment Mention #

[![WPCS](https://github.com/BhargavBhandari90/comment-mention/actions/workflows/wpcs.yml/badge.svg?branch=master)](https://github.com/BhargavBhandari90/comment-mention/actions/workflows/wpcs.yml)
[![E2E test](https://github.com/BhargavBhandari90/comment-mention/actions/workflows/e2e.yml/badge.svg?branch=master)](https://github.com/BhargavBhandari90/comment-mention/actions/workflows/e2e.yml)

**Contributors:** [bhargavbhandari90](https://profiles.wordpress.org/bhargavbhandari90/), [biliplugins](https://profiles.wordpress.org/biliplugins/), [hackkzy404](https://profiles.wordpress.org/hackkzy404/)  
**Donate link:** https://www.paypal.me/BnB90/50  
**Tags:** comments, mention, email, user, bbpress  
**Requires at least:** 4.6  
**Tested up to:** 6.8.1  
**Stable tag:** 1.7.19  
**Requires PHP:** 5.6  
**License:** GPLv2 or later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html  

Mention user in comments without BuddyPress plugin. Mentioned user will get email notification.

Compatible with BBPress as well.

## Description ##

Now you can enable user mention functionality in post comments without using BuddyPress plugin.

This plugin is useful for those who wants to enable mention on their blog site.

What this plugin does? Just wring username followed by '@' in comment box. It will send the email to mentioned user.

Email setting is provided in backend. You can change email subject and content by your own.

https://www.youtube.com/watch?v=Nz47aKJhsKQ

## PRO Features ##

* Search by First/Last name while mention.
* Search by Display name while mention.
* Enable mentioning on Page comment.
* Added option to Turn off Email notification.
* Mention by First Name & Last Name ( Under development... ).
* Go to wp-admin –> Comment Mention
* And you will see options to enable pro features https://prnt.sc/r5W2X4utYe3v

* [Get Comment Mention Pro](https://biliplugins.com/comment-mention-pro-product/)
* TO USE THIS PRO PLUGIN, MAKE SURE YOU HAVE AT LEAST Comment Mention PLUGIN v1.3.0

## Pro Plugin Compatibility ##

* [GamiPress](https://gamipress.com/?ref=203)
* [MemberPress](https://memberpress.com/)

## Installation ##

This section describes how to install the plugin and get it working.

## Screenshots ##
1. Mention the user by typing "@" in the comment box.
2. Setting for sending email to mentioned user.

e.g.

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Comment Mention menu to configure the plugin

## GIT Repo ##

[https://github.com/BhargavBhandari90/comment-mention](https://github.com/BhargavBhandari90/comment-mention)

## Development Prerequisites
- [Node/NPM](https://nodejs.org/en/download/)
- [NVM](https://github.com/nvm-sh/nvm)
- [Docker](https://www.docker.com/)

## Development Setup
1. Go to plugin's root
2. Run `npm install`

## End to End Testing

Start Local WordPress Environment, run:

	npm run start:env

To run e2e test, run:

	 npm run test:e2e


## Changelog ##

### 1.7.19 ###
* Fixed - Mentioning users in bbPress topic and reply when bbp style pack is active.

### 1.7.18 ###
* Improvements based on PHP 8.x.
* Fixed - Displaying span tag in bbPress replies.

### 1.7.17 ###
* Fixed - Pro links were displaying even pro plugin is active.

### 1.7.16 ###
* Removed case-sensitivity while searching users.

### 1.7.15 ###
* Added option to enable avatars for mentioned users.

### 1.7.14 ###
* Mention script improvisation for pro plugin.
* Added hooks for pro plugin.

### 1.7.13 ###
* Mention script improvisation for pro plugin.

### 1.7.12 ###
* Mention script improvisation.

### 1.7.11 ###
* Hook added : `cmt_mntn_comment_pre_content`.
* Updated mention script for pro feature.

### 1.7.10 ###
* Fix bbPress reply link.

### 1.7.9 ###
* Bug Fix : Double quotes issue with Email subject.

### 1.7.8 ###
* Hook added : `cmnt_mntn_replace_mentioned_name`.

### 1.7.7 ###
* Bug Fix : Unable to mention user on freshly installed `Comment Mention`.

### 1.7.6 ###
* Update plugin issue fix.

### 1.7.5 ###
* Minor bug fix.

### 1.7.4 ###
* Minor bug fix.

### 1.7.3 ###
* Added shortcode support for Email subject.

### 1.7.2 ###
* Minor Updates.

### 1.7.1 ###
* Minor Bug Fixes.

### 1.7.0 ###
* Mention script Updated.
* Minor Bug Fixes.

### 1.6.0 ###
* Added Option to Hide selected user roles while mentioning.

### 1.5.0 ###
* Added Option to enable comment mention for selected user roles.

### 1.4.6 ###
* JS & CSS optimization.

### 1.4.5 ###
* JS & CSS optimization.

### 1.4.4 ###
* Fix - Not able to mention.

### 1.4.3 ###
* Fix - Not able to mention.

### 1.4.2 ###
* Made some changes related to pro plugin.

### 1.4.1 ###
* Made some changes related to pro plugin.

### 1.4.0 ###
* Added support for TinyMCE.

### 1.3.3 ###
* Minor bug fixes.

### 1.3.2 ###
* Changes for pro plugin.

### 1.3.1 ###
* Changes for pro plugin.

### 1.3.0 ###
* Changes for pro plugin.

### 1.2.5 ###
* Added actions and filters.

### 1.2.4 ###
* Added new placeholders: #commenter_name# and #comment_content#.

### 1.2.3 ###
* Fix issue of ajax with WP 5.5.

### 1.2.2 ###
* Fix issue of class not added for mentions added by other than administrator.

### 1.2.1 ###
* Fix issue of mention with post comment.

### 1.2.0 ###
* Add bbpress compatibility.

### 1.1.0 ###
* Add support for language.

### 1.0.1 ###
* Fix issue of sending two mails to mentioned users.

### 1.0.0 ###
* Initial Release.
