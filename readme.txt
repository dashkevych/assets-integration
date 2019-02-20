=== Plugin Name ===
Contributors: taskotr
Tags: assets, js, css, bootstrap
Requires at least: 4.9
Tested up to: 5.1
Requires PHP: 7.0
Stable tag: 1.0.0
License: GPL-3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

The easiest way to load Bootstrap assets (JS & CSS) in WordPress.

== Description ==

Assets Integration is a plugin that allows to load additional assets on your WordPress site. Currently, the plugin supports only the Bootstrap framework. This web framework can be loaded either locally or via CDN. It is also possible to specify which type of the asset you want to load: CSS or JS or both.

= Bugs report =

If you find an issue with the Assets Integration plugin, let us know [here](https://github.com/dashkevych/assets-integration/issues)!

== Screenshots ==

== Installation ==

**Requires PHP 7.0+**

Assets Integration can be installed in a few easy steps:

= USING THE WORDPRESS DASHBOARD: =

1. Navigate to the 'Add New' in the plugins dashboard.
2. Search for 'assets integration'.
3. Locate the plugin and click 'Install Now'.
4. Activate the Assets Integration plugin through the 'Plugins' menu in WordPress.

= MANUALLY UPLOAD: =

1. Upload the `assets-integration` folder to your `/wp-content/plugins/` directory or alternatively upload the assets-integration.zip file via the plugin page of WordPress by clicking 'Add New' and selecting the zip from your computer.
2. Activate the Assets Integration plugin through the 'Plugins' menu in WordPress.

Once the plugin is installed and activated, navigate to the Assets page in your WordPress dashbaord, and select the asset you want to load on your site.

== Frequently Asked Questions ==

= How do I load assets locally? =

On the asset page in dashbaord, do the following: 

1. Check the "Load assets locally" option in the Asset delivery section.
2. Select a version of the asset you want to load on your site in the Version section.
3. Select a type of the asset you want to load on your site in the Asset Type section. It can be either JavaScript, or CSS, or both.
4. Click Save Changes to save your settings.

= How do I load assets via CDN? =

On the asset page in dashbaord, do the following: 

1. Check the "Load assets via CDN" option in the Asset delivery section.
2. Add URLs of the asset located on the CDN in the Asset Type section.
4. Click Save Changes to save your settings.

== Changelog ==

= 1.0.0 =
Initial release.