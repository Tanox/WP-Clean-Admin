=== WP Clean Admin ===
Contributors: tanox
Tags: admin, cleanup, optimization, performance, security, menu
Requires at least: 5.0
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.9.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A comprehensive WordPress admin cleanup and optimization plugin.

== Description ==

WP Clean Admin helps you keep your WordPress dashboard fast, clean and secure:

* Clean up redundant admin menus and features.
* Optimize admin loading performance (scripts, styles, embeds, REST restrictions).
* Enhance backend security (login hardening, captcha, two-factor authentication).
* Flexible menu customization and user role/permission management.
* Simplified database management and cleanup (revisions, transients, spam, media).
* Diagnostics panel with module health and conflict checks.
* Developer-friendly extension API.

= Contribute =

Development and issue tracking take place on GitHub:
https://github.com/Tanox/WP-Clean-Admin

== Installation ==

1. Upload the `wpcleanadmin` folder to the `/wp-content/plugins/` directory.
2. Activate "WP Clean Admin" through the "Plugins" menu in WordPress.
3. Configure the plugin from the "WP Clean Admin" settings page.

== Frequently Asked Questions ==

= Does the plugin work on multisite? =

The plugin is designed and tested for single-site installations.

= Will cleanup delete content I need? =

Cleanup actions are explicit and scoped (revisions, transients, spam comments,
orphaned media). Always use the built-in backup option before running
database cleanups.

== Changelog ==

For the full changelog, see CHANGELOG.md in the plugin repository:
https://github.com/Tanox/WP-Clean-Admin/blob/main/CHANGELOG.md

== Upgrade Notice ==

= 1.9.0 =
Fixes a module autoloading fatal error, hardens two-factor authentication and
AJAX handlers, adds uninstall data cleanup and a WordPress.org readme.
