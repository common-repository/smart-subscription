
=== Smart Subscription ===
Contributors: Avezatech
Tags: form, subscribe, email, download, redirect, export
Donate link: http://wpplugins.avezatech.com/donate/
Requires at least: 3.0.1
Tested up to: 4.3
Stable tag: 0.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Smart Plugin is a plugin that allows to distribute files through subscriptions.

== Description ==
Smart Plugin allows website owner to give away PDF or other free resources after collecting email address from the website visitor and also redirect the visitor to desired landing page at the same time. Submitted data is saved in database and can be viewed and downloaded as CSV file in admin area.


[Live Preview](http://wpplugins.avezatech.com/smart-subscription/)

== Installation ==
1. Upload the entire Smart Plugin folder to the /wp-content/plugins/ directory.
1. Activate the plugin through the \'Plugins\' menu in WordPress.
1. Once activated, it will create a menu 'Smart Subscription' in left side column in the admin area.
1. Click left side menu “Smart Subscription  >>> Add New” and do required settings. Set your Form Name, Download and redirect page settings, etc.
1. Go to any post/page edit page and insert shortcode like that: [smart-subscription-form id=\"1\" title=\"Download and redirect form\"]


== Frequently Asked Questions ==

= Can i download the file and redirect to specfic URL? =

Yes, you can.

= Whether i can use only download option or redirect option? =

yes, you can give the option only to download or else only to redirect.

= Whether i can see the submitted subscribers? =

yes, you can see the subscribers form by form in admin.

= How many times a subscriber can download the files? =

The subscriber can download the files 'n' times.

= How can I add a subscribe form into my post/page content? =

Each subscribe form has its own tag [(shortcode)](http://codex.wordpress.org/Shortcode), such as [smart-subscription-form id="1234" title="Smart Subscription at one click"]. To insert the subscribe form into your post/page, copy the shortcode and paste it into the post/page content.

= Can I place a contact form outside a post? =

Yes. You may place a subscribe form in a text widget as well.

= Can I embed a contact form into my template file? =

<?php echo do_shortcode( '[smart-subscription-form id="1234" title="Smart Subscription at one click"]' ); ?>

= How can I export subscribe form data? =

Smart Subscription -> Export

Do you have questions or issues with Contact Form 7? Use these support channels appropriately.

1. [Docs](http://wpplugins.avezatech.com/smart-subscription/docs/)
1. [FAQ](http://wpplugins.avezatech.com/smart-subscription/faq/)
1. [Support Forum](http://wordpress.org/support/plugin/smart-subscription)

[Support](http://wpplugins.avezatech.com/smart-subscription/support/)

== Screenshots ==
1. screenshot-1.png
2. screenshot-2.png
3. screenshot-3.png
4. screenshot-4.png
5. screenshot-5.png

== Changelog ==
= 0.1 =
* Initial release.


== Upgrade Notice ==
= 1.2 =
Added plugin support
