=== GDPR Comments ===
Contributors: webappdesign
Tags: comments, gdpr
Requires at least: 4.9.7
Tested up to: 5.4
Stable tag: trunk
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This plugin allow you to add a checkbox to accept Privacy Policy before add a comment.

== Description ==

The General Data Protection Regulation (RGPD) that came into effect on May 25, 2018, oblige the owners of the websites to inform users of the following aspects:

* Who is responsible for the processing of your data?
* What is the purpose for which personal data are collected?
* What is the legal basis for collecting the data?
* Who can be the recipients of personal data?
* What are the rights that users can exercise in terms of data protection?

This information, in addition to the Privacy Policy of the web, must be present in a first layer of basic information below each form or data collection channel of the web.

Another obligation for the owners of the websites is to obtain the consent of the users, explicitly, in order to collect and process their data. To obtain this consent, the user must take an action (such as check a checkbox), that is, the users must be aware that they are giving their consent for a specific purpose.

WordPress, although it included in the comment form of the blog a checkbox so that users could save their data in a cookie to be used in the future, didn't put neither the link to the Privacy Policy nor the first layer of information, obligatory by the regulation. For this reason, this plugin is born, to add those elements and thus the owners of the websites can comply with the law.

== Installation ==

1. Upload the plugin files to the "/wp-content/plugins/webappdesign-gdpr-comments" directory, or install the plugin through the WordPress plugins screen directly
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the Settings >> GDPR Comments screen to configure the plugin


== Screenshots ==

1. This is the plugins options screen.
2. Checkbox with Privacy Policy Link and First Layer of Information

== Changelog ==

= 1.0 =
* This is the first version of the plugin

= 1.1 =
* Fixed errors with text-domain and plugin translations

= 1.2 =
* Move plugin data from custom table to WordPress options table
* Remove custom table
* Fix the error that prevented posting comments from the dashboard