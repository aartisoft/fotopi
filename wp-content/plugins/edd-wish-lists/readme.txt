=== EDD Wish Lists ===

== Installation ==

1. Unpack the entire contents of this plugin zip file into your `wp-content/plugins/` folder locally
1. Upload to your site
1. Navigate to `wp-admin/plugins.php` on your site (your WP Admin plugin page)
1. Activate this plugin

After activation, configure the plugin from downloads -> settings -> extensions

== Changelog ==

= Version 1.1.7, July 17, 2017 =

* Fix: "Remove" icon not showing for each download on individual wish list pages

= Version 1.1.6, June 18, 2017 =

* Fix: PHP fatal error on PHP 7.1.1
* Fix: Wish List buttons do not work on Download taxonomy pages
* Fix: Missing close button icons

= Version 1.1.5, May 31st, 2017 =
* Fix: Non-numeric warning message on PHP 7.1.3

= Version 1.1.4, February 14th, 2017 =
* Tweak: Modified CSS selectors so there's a lesser chance of conflicts
* Tweak: Added edd_wl_query_args filter hook

= Version 1.1.3, August 12, 2016 =
* Tweak: ensure Wish List post type does not display in menu builder

= Version 1.1.2, January 2, 2016 =
* Fix: Variable-priced downloads were sometimes added twice to a wish list when another EDD purchase form existed on the same page
* Fix: Replaced deprecated post_permalink() function with get_permalink()
* Fix: Empty paragraph tag on the wish-list-view.php template when there no wish list description
* Tweak: Prevented "Add to wishlist" buttons from appearing on the EDD checkout page where extensions such as Cross-sell/Upsell & Recommended Products add "add to cart" buttons.
* Tweak: Replaced a soon to be deprecated WordPress function
* New: The quantity field (if enabled in EDD) is now shown on add to cart buttons
* New: Headings in template files now have a edd-wl-heading CSS class for easier styling
* New: Wrapped the view template with a "edd-wl-view" CSS class for easier styling
* New: Wrapped the edit template with a "edd-wl-edit" CSS class for easier styling
* New: Wrapped the create template with a "edd-wl-create" CSS class for easier styling
* New: Wrapped the main wish lists template with a "edd-wl-wish-lists" CSS class for easier styling
* New: Added a edd_wl_disable_on_checkout filter for adding "Add to wishlist" buttons back to the checkout if desired (see Tweak note above)

= Version 1.1.1, April 20, 2015 =
* Fix: XSS vulnerability in query args

= Version 1.1, March 10th, 2015 =
* Fix: Some plugins which flushed rewrite rules on activation interferred with Wish Lists' rewrite rules
* Fix: When sharing a wish list via Facebook, the correct URL is now shared
* Fix: Issue with sharing URL disappearing from single wish list page when WP.me Shortlinks were enabled in Jetpack
* Tweak: Various opengraph improvements

If you have made modifications to the wish-list-view.php template, make sure edd_wl_wish_list_item_purchase() is renamed to edd_wl_item_purchase()

= Version 1.0.9, February 10th, 2015 =
* Fix: Cart quantities in some themes were being updated when a download was added to a wish list.
* Fix: When EDD was deactivated and reactivated the wish list page was not viewable until either the EDD settings were saved or Wish Lists was deactivated and reactivated.
* Tweak: Leaving "Enable Ajax" unchecked in downloads -> settings -> misc no longer affects Wish Lists. Ajax is always required for Wish Lists so now works regardless of this setting.

= Version 1.0.8, January 5th, 2015 =
* Tweak: Improved edd_wl_get_list_id() and edd_wl_get_wish_list() functions
* Tweak: Modified wish-list-edit.php and wish-list-view.php templates based on the changes above

= Version 1.0.7, January 1st, 2015 =
* Fix: LinkedIn issue when loading over https
* Tweak: When removing an item from a wish list, it now searches for the closest element with a CSS class of "row". This means you can structure your HTML how you want and only need to apply the row class to the wrapper that should be removed.
* Tweak: Removed html { overflow-y: inherit; } CSS rule
* Tweak: New activation class
* Tweak: Changed the "edit" and "view" query vars to "wl_edit" and "wl_view". This was to avoid potential conflicts with other plugins using the same names. If you have modified the wish-list-edit.php template make sure to change line 6 from get_post( get_query_var('edit') ) to get_post( edd_wl_get_wish_list() );

= Version 1.0.6, May 7th, 2014 =
* Fix: Compatibility with EDD v1.9.9
* Fix: Modified a redirect action so it doesn't conflict with other plugin redirects
* Fix: Added a default value for the $id in the edd_wl_the_title() function.

= Version 1.0.5, March 21, 2014 =
* Fix: shortcodes weren't showing on page templates when Wish Lists plugin was active

= Version 1.0.4, March 19, 2014 =
* New: edd_wl_allowed_post_types() function
* New: edd_wl_item_title_permalink filter hook
* Fix: issue where add to cart button wasn't working on wish list page when variable priced download was used

= Version 1.0.3, March 11, 2014 =
* Fix: when no text is shown in settings, don't show default text on front-end
* Fix: filter with same name as another
* Fix: removed options passed into the edd_wl_delete_list_link function on the wish-list-edit.php template. These were overriding (as they should) the plugin's options.
* Fix: link size CSS class names.
* Tweak: small CSS adjustment for when add to wish list link does not have any text. The icon now aligns better
* Tweak: Moved text from delete list modal into the edd_wl_messages() function

= Version 1.0.2, March 10, 2014 =
* New: added email sharing as option in extension settings
* New: added filter to remove delete link
* Tweak: improved script handling
* Tweak: JS
* Tweak: CSS
* Tweak: improved script loading
* Tweak: improved handling of list queries
* Tweak: admin settings improvements
* Tweak: code refactoring
* Tweak: drastically reduced code in view and wish lists templates
* Tweak: Create new list button is now hidden when create page is not selected in settings
* Tweak: Edit links are now hidden when no edit page selected in options

= Version 1.0.1, February 20, 2014 =
* Fix: PHP 5.2 Compatibility
* Tweak: Different list creation messages for guest/logged in users

= Version 1.0, February 17, 2014 =
* Initial release
