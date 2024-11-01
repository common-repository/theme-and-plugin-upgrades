=== Theme And Plugin Upgrades ===
Contributors: wphoundstore
Donate link: https://www.paypal.me/wphound/20
Tags: plugin, theme, upgrade, update, upload , new realese
Requires at least: 4.0
Tested up to: 4.8
Stable tag: 3.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily upgrade your themes and plugins using zip files without removing the theme or plugin first.

== Description ==

WordPress has a built-in feature to install themes and plugins by supplying a zip file. Unfortunately, you cannot upgrade a theme or plugin using the same process. Instead, WordPress will say "destination already exists" when trying to upgrade using a zip file and will fail to upgrade the theme or plugin.

Theme and Plugin Upgrades fixes this limitation in WordPress by automatically upgrading the theme or plugin if it already exists.

While upgrading, a backup copy of the old theme or plugin is first created. This allows you to install the old version in case of problems with the new version.

= How do I upgrade a theme? =

1. Download the latest zip file for your theme.
2. Log into your WordPress site.
3. Go to Appearance > Themes.
4. Click the "Add New" button at the top of the page.
5. Click the "Upload Theme" button at the top of the page.
6. Select the zip file with the new theme version to install.
7. Click the "Install Now" button.

= How do I upgrade a plugin? =

1. Download the latest zip file for your plugin.
2. Log into your WordPress site.
3. Go to Plugins > Add New.
4. Click the "Upload Plugin" button at the top of the page.
5. Select the zip file with the new plugin version to install.
6. Click the "Install Now" button.

= How do I access the backup of an old theme or plugin? =

1. Log into your WordPress site.
2. Go to Media > Library.
3. Type "backup" into the search input and press the "Enter" key.
4. Find the desired backup from the resulting list.
5. Click the title of the desired backup.
6. The URL to the backup file is listed on the right side of the page under "File URL". You can copy and paste that URL into your browser's URL bar in order to start a download.



== Installation ==

1. Download and unzip the latest release zip file
2. Upload the entire theme-and-plugin-upgrades directory to your `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

If you have any question do let me know on care@wphound.com


== Changelog ==

= 1.0 =
Initial Release of the plugin