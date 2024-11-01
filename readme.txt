=== WordPress Pastebin ===
Contributors: nkuttler
Author URI: http://www.nkuttler.de/
Plugin URI: http://www.nkuttler.de/wordpress/wordpress-pastebin/
Donate link: http://www.nkuttler.de/wordpress/donations/
Tags: admin, plugin, paste, pastebin, i18n, l10n, internationalized, localized
Requires at least: 3.0
Tested up to: 3.0
Stable tag: 0.4.5.4

Turn your blog into a pastebin with syntax highlighting provided by other plugins.

== Description ==

Turn your blog into a pastebin and keep your own code on your own site. You can tag your pastes like you tag your normal posts, and visitors can leave comments if you allow it.

I wrote this because it makes it easier for me to share code. Plus I can easily search my own pastes.

Front end editing of pastes and posting for anonymous users is not enabled by default, and I didn't write a captcha for anonymous posting. Anonymous user's can't tag or categorize their pastes either. Please submit patches if you need such features.

The plugin comes with a widget to list recent pastes.

See a [paste example](http://nkuttler.de/paste/1il/) on my website.

This plugin can use the following plugins to do syntax highlighting when they are installed:

* [WP-Syntax](http://wordpress.org/extend/plugins/wp-syntax/)
* [SyntaxHighlighter Plus](http://wordpress.org/extend/plugins/syntaxhighlighter-plus/)
* [Syntax Highlighter and Code Prettifier](http://wordpress.org/extend/plugins/syntax-highlighter-and-code-prettifier/)

If your syntax highlighter is missing or doesn't work please do the following:

1. Add recognition for your plugin in get_highlighter_list().
1. Add support for your plugin in add_tags().
1. Send me a patch.

= Other plugins I wrote =

[WordPress Pastebin](http://www.nkuttler.de/): Keep your own code on your own site. Short permalinks, tagging, commenting, etc.

[Better Lorem Ipsum](http://www.nkuttler.de/wordpress-plugin/wordpress-lorem-ipsum-generator-plugin/): Auto-generate lorem ipsum content for all post types and taxonomies. Does comments as well. For theme and plugin developers.

[Custom Avatars For Comments](http://www.nkuttler.de/wordpress/custom-avatars-for-comments/): Your visitors will be able to choose from the avatars you upload to your website for each and every comment they make.

[Better tag cloud](http://www.nkuttler.de/wordpress/nktagcloud/): I was pretty unhappy with the default WordPress tag cloud widget. This one is more powerful and offers a list HTML markup that is consistent with most other widgets.

[Theme switch](http://www.nkuttler.de/wordpress/nkthemeswitch/): I like to tweak my main theme that I use on a variety of blogs. If you have ever done this you know how annoying it can be to break things for visitors of your blog. This plugin allows you to use a different theme than the one used for your visitors when you are logged in.

[Zero Conf Mail](http://www.nkuttler.de/wordpress/zero-conf-mail/): Simple mail contact form, the way I like it. No ajax, no bloat. No configuration necessary, but possible.

[Move WordPress comments](http://www.nkuttler.de/wordpress/nkmovecomments/): This plugin adds a small form to every comment on your blog. The form is only added for admins and allows you to [move comments](http://www.nkuttler.de/nkmovecomments/) to a different post/page and to fix comment threading.

[Delete Pending Comments](http://www.nkuttler.de/wordpress/delete-pending-comments): This is a plugin that lets you delete all pending comments at once. Useful for spam victims.

[Snow and more](http://www.nkuttler.de/wordpress/nksnow/): This one lets you see snowflakes, leaves, raindrops, balloons or custom images fall down or float upwards on your blog.

== Installation ==

1. Unzip
2. Upload to your plugins directory
3. Enable the plugin
4. Configure it as you like
5. Add new pastes under Paste->Add New

== Screenshots ==

1. The options page.

== Frequently Asked Questions ==

Q: I don't like the /paste/ base, what can I do.<br />
A: Define WORDPRESS_PASTEBIN_POST_TYPE_SLUG in your wp-config.php. Please notice that you will have to take care of redirecting old pastes to the new slug yourself. You can use a .htaccess or any other method you like to do that.

Q: I get an error that the post type already exists.<br />
A: Define WORDPRESS_PASTEBIN_POST_TYPE_NAME in your wp-config.php

== Changelog ==
= 0.4.5.4 ( 1020-10-22 ) =
 * Suppress filters on recent widget
= 0.4.5.2 ( 1020-10-22 ) =
 * Add support for syntax-highlighter-and-code-prettifier
= 0.4.4 ( 1020-10-18 ) =
 * Add to the plugin repository
= 0.4.2 ( 1020-10-16 ) =
 * Add front end posting and editing
 * Remove custom taxonomy
= 0.2 ( 1020-10-05 ) =
 * Switch to a custom taxonomy
 * Add auto-tagging
 * Add auto-category
 * Add recent pastes widget and fix rewrite rule flushing
 * Many improvements and bug fixes
= 0.0.1 ( 2010-10-01 ) =
 * First working version
