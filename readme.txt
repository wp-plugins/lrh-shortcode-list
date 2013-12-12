=== LRH-Shortcode List ===
Contributors: lrhguy
Donate link: http://lrh.net
Tags: lrh shortcode list,shortcode list,shortcodes
Requires at least: 3.0.0
Tested up to: 3.8-RC2
Stable tag: 1.2.0
License: GPLv2 or later

Display a list of available shortcodes in a meta box for inserting into content.

== Description ==

This WordPress plugin will display a list of available shortcodes in a meta box for
inserting into content. Insertions add brackets and any known required
parameters. The meta box can also display help about shortcodes, if any,
by clicking on the name.

An admin settings section allows the activation and deactivation of listed
shortcodes. Also shown is any additional known information, such as
required and optional parameters.

= Developers =

You can provide addition information about your shortcode by responding
to a filter 'sim_XXX' where XXX is your shortcode tag.

The additional information can include description, is it self-closing,
the required parameters, and the optional parameters.

- Providing Shortcode Information -

To provide additional information to the helper, create a filter that
updates and returns a array of data.
See http://lrh.net/wpblog_lrh/lrh-shortcode-list-wp-plugin/ for examples.


== Installation ==

Install into /wp-content/plugins/lrhshortcodelist directory.

== Frequently Asked Questions ==

None at this time.

== Screenshots ==

1. The metabox in the Edit Post.
2. The shortcodes manager.
3. The metabox showing additional information.

== Changelog ==

= 1.0 =
* Initial version.

= 1.2.0 =
* Clean up for repository

== Upgrade Notice ==

= 1.2.0 =
* This is the newest release
