=== Interactive Polish Map  ===
Contributors: iworks
Donate link: https://ko-fi.com/iworks?utm_source=interactive-polish-map&utm_medium=readme-donate
Tags: map, polish, interactive, svg, responsible
Requires at least: PLUGIN_REQUIRES_WORDPRESS
Tested up to: PLUGIN_TESTED_WORDPRESS
Stable tag: PLUGIN_VERSION
Requires PHP: PLUGIN_REQUIRES_PHP
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

PLUGIN_DESCRIPTION

== Description ==

The best way to convert a list of Polish regions into stylish clickable SVG map.

The map is fully responsible - no any action from you is required.

== Installation ==

There are 3 ways to install this plugin:

= The super easy way =

1. **Log in** to your WordPress Admin panel.
1. **Go to: Plugins > Add New.**
1. **Type** ‘Interactive Polish Map’ into the Search Plugins field and hit Enter. Once found, you can view details such as the point release, rating and description.
1. **Click** Install Now. After clicking the link, you’ll be asked if you’re sure you want to install the plugin.
1. **Click** Yes, and WordPress completes the installation.
1. **Activate** the plugin.
1. A new menu `Interactive Polish Map` in `Settings` will appear in your Admin Menu.
1. Place **[mapa-polski]** in your post or page.

***

= The easy way =

1. Download the plugin (.zip file) on the right column of this page
1. In your Admin, go to menu Plugins > Add
1. Select button `Upload Plugin`
1. Upload the .zip file you just downloaded
1. Activate the plugin
1. A new menu `Interactive Polish Map` in `Settings` will appear in your Admin Menu.
1. Place **[mapa-polski]** in your post or page.

***

= The old and reliable way (FTP) =

1. Upload `interactive-polish-map` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. A new menu `Interactive Polish Map` in `Settings` will appear in your Admin Menu.
1. Place **[mapa-polski]** in your post or page.

== Changelog == 

= 2.0.3 - 2025-02-23 =
* The [iWorks Options](https://github.com/iworks/wordpress-options-class) module has been updated to 2.9.6.
* The [iWorks Rate](https://github.com/iworks/iworks-rate) module has been updated to 2.2.3.

= 2.0.2 - 2023-03-13 =
* The [iWorks Options](https://github.com/iworks/wordpress-options-class) module has been updated to 2.9.2.
* The [iWorks Rate](https://github.com/iworks/iworks-rate) module has been updated to 2.1.9.
* The function `strip_tags()` has been replaced by the function `wp_strip_all_tags()`.
* The function `rand()` has been replaced by the function `wp_rand()`.

= 2.0.1 - 2021-01-20 =
* The [iWorks Options](https://github.com/iworks/wordpress-options-class) module has been updated to 2.7.3.
* The [iWorks Rate](https://github.com/iworks/iworks-rate) module has been updated to 2.0.6.

= 2.0.0 - 2021-07-08 =
* Replaced png by svg image.
* Added Gutenberg block.
* The [iWorks Rate](https://github.com/iworks/iworks-rate) module has been updated to 1.0.3.
* The [iWorks Options](https://github.com/iworks/wordpress-options-class) module has been updated to 2.6.9.

= 1.1 - 2015-08-08 =
* IMPROVEMENT: Update method of WP_Widget - remove deprecated constructor.
* BUGFIX: Handle some notices about undefined variables.

= 1.0.2 - 2012-03-03 =
* BUGFIX: Shortcode always place map on top of entry (echo was used instead of return)

= 1.0.1 - 2011-02-04 =
* BUGFIX: Shortcode always place map on top of entry (echo was used instead of return)

= 1.0 - 2011-02-04 =
* NEW: Added widget.
* NEW: Added option to choose list.
* NEW: Added Polish translation.
* NEW: Created POT file.
* Changed: js files was merged to one.
* Changed: css files was merged to one.

= 0.1 - 2011-01-29 =
* NEW : Init revision.

