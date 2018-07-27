=== YODA WP ===
Contributors: Genesys Employees
Tags: guide announcement notification translations github bitbucket
Github: https://github.com/smargovsky/yoda.wp

Plugin creates two custom post types, announcements and guides, for displaying walkthroughs on your webiste. For use alongside YODA.JS.

== Description ==

YODA.WP handles creating guides that can act as annoucements or walkthrough guides on your website. They are authored through two Wordpress custom post types for curation by non technical editors. Once created, these guides are available via a specific set of Wordpress API endpoints.

NOTE: This plugin is intended to be used alongside YODA.JS, the front-end SDK that consumes this plugin's API, and intelligently displays guides on your website.

YODA.JS can be obtained here: https://github.com/smargovsky/yoda.js

== Installation ==

1. Upload `yoda-wp` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. If you wish to enable translation support, make a copy of the .env.sample as .env, and update the Git username, password, and repository URL.
1. Create any number of Announcements or Guides from the Wordpress Admin area.
1. Follow the installation instructions for YODA.JS on your website.

NOTE: YODA.WP is packaged with the vendor folder. If any issues arise, remove this folder and reinstall the PHP dependencies using `composer install` from the plugin folder.

== Changelog ==

= 1.0 =
* YODA.WP Beta release
* Initial release with more iterations coming soon!