=== Marketify ===
Contributors: Astoundify
Requires at least: WordPress 4.8.0
Tested up to: WordPress 4.9.7
Version: 2.16.0
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Tags: white, two-columns, one-column, right-sidebar, left-sidebar, fluid-layout, custom-background, custom-header, theme-options, full-width-template, featured-images, flexible-header, custom-menu, translation-ready

== Copyright ==

Marketify Theme, Copyright 2014-2015 Astoundify -
Marketify is distributed under the terms of the GNU GPL.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

The Marketify theme bundles the following third-party resources:

Bootstrap v3.0.3
Copyright 2013 Twitter, Inc
Licensed under the Apache License v2.0
http://www.apache.org/licenses/LICENSE-2.0

Slick.js v1.5.7, Copyright 2015 Ken Wheeler
Licenses: MIT/GPL2
Source: https://github.com/kenwheeler/slick/

Magnific-Popup Copyright (c) 2014-2015 Dmitry Semenov (http://dimsemenov.com)
Licenses: MIT
Source: https://github.com/dimsemenov/Magnific-Popup

vide 0.4.1 Copyright (c) 2015 Ilya Makarov
Licenses: MIT
Source: https://github.com/VodkaBears/Vide

Ionicons icon font, Copyright (c) 2014 Drifty (http://drifty.com/)
License: MIT
Source: https://github.com/driftyco/ionicons

== Changelog ==

= 2.16.0: July 25, 2018 =

* New: Update to Bootstrap v4. Needs heavy testing for columns, layout, etc.
* Fix: Safari grid overflow.
* Fix: Respect downloads per page setting in customizer.
* Fix: Minimal page template background height on mobile.

= 2.15.0: April 3, 2018 =

* New: Full support for Easy Digital Downloads - Reviews.
* Fix: Featured/Popular mobile slider height.
* Fix: Do not use custom per page arguments on auto output [downloads] shortcode.

= 2.14.2: January 15, 2017 =

* Fix: Update Media Element playlist controls.

= 2.14.1: December 28, 2017 =

* Fix: Better EDD 2.8.16 compatibility for all download types and options.

= 2.14.0: December 22, 2017 =

* New: Allow File Upload field in FES to be used to set a video.
* Fix: EDD 2.8.16 compatibility.
* Fix: Updated automatic theme updater.

= 2.13.0: November 29, 2017 =

* New: Use CSS flexbox for simpler more response columns (fixes support for download grid and FacetWP).
* New: Use theme version for asset versioning to help clear caches.
* Fix: Integration file loading on Windows-powered machines.

= 2.12.0: September 15, 2017 =

* New: Update coding standards.
* New: Convert footer to use all widgets.
* Fix: Check correct customizer setting for outputting download sidebar details.
* Fix: Title truncation checks.
* Fix: Easy Digital Downloads 2.8.6 compatibility.

= 2.11.1: June 26, 2017 =

* Fix: Avoid PHP error in theme customizer.
* Fix: Borders around download details widget.
* Fix: Ensure download type settings are backwards compatible.
* Fix: Ensure customizer navigation text color is respected.

= 2.11.0: May 4, 2017 =

* New: Enhanced customization of colors and typography in "Appearance ▸ Customize"
* Fix: EDD Bookings compatibility.
* Fix: EDD Custom Prices compatibility.

= 2.10.0: January 30, 2017 =

* New: Allow vendor widgets to be placed on download sidebar (contact, description, about).
* Fix: Easy Digital Downloads - External Products style fixes.
* Fix: Do not output empty video error when no video exists.
* Fix: Add filter to "Purchase" button so other plugins can update when needed.
* Fix: Ensure close button is visible on popups taller than the window.

= 2.9.0: October 12, 2016 =

* New: EDD Cross Sells and Upsells integration. https://easydigitaldownloads.com/downloads/edd-cross-sell-and-upsell/
* New: EDD Upload File integration. https://easydigitaldownloads.com/downloads/edd-upload-file/
* New: EDD Booking integration. https://easydigitaldownloads.com/downloads/edd-bookings/
* New: Allow page header tags to be filtered for preferred SEO technique.
* New: Allow shortcodes to be parsed in Footer Contact text area.
* New: Allow featured video to be pulled from attached media.
* Fix: Ensure customizer panels load in the correct order.
* Fix: Checkout section title display when switching payment gateways.
* Fix: Disable duplicate recommended products from displaying when switching payment gateways.
* Fix: Disable schema micro data on recommended products.
* Fix: Use correct FES shortcodes on import.
* Fix: Ensure function exists before using in dynamic widget title.

= 2.8.1: July 26, 2016 =

* Fix: Safer upgrade routines and checks.

= 2.8.0: July 26, 2016 =

* New: EDD Reviews 2.0 support.
* New: Audio download preview play button to the left of the download title.
* New: Discounts Pro support.
* Fix: Improve content importer.
* Fix: Ensure mobile menu and menu dropdowns appear above page titles.

= 2.7.1: June 3, 2016 = 

* Fix: Update Content Importer drop-in to avoid PHP notices.
* Fix: Remove Setup Guide images from package.

= 2.7.0 =

* New: Automatic theme updates via ThemeForest.
* New: Automatic child theme creation while maintaining customizer settings.
* New: Content importer in Setup Guide.
* Fix: Touch events for download grids on mobile devices.
* Fix: Avoid large download sizes before columns are formed.
* Fix: Mobile checkout/cart experience improvements.

= 2.6.0: March 28, 2016 =

* New: Add oEmbed support for Audio downloads. Create a URL field with the `audio` meta key and add a URL.
* New: EDD Product Badges styling support.
* Fix: Wish List Purchase/Checkout button spacing.
* Fix: Be sure WordPress core playlists continue to function after sorting with FacetWP.
* Fix: Password repeat field in Frontend Submsissions profile form.

= 2.5.0: February 16, 2016 =

* New: Author archive width when no sidebar is used matches blog.
* Fix: When using variable prices ensure the Buy Now modal displays correctly.
* Fix: Include Featured Callout demo images in the theme images directory.

= 2.4.0: February 5, 2016 = 

* New: Widgetized Pages. See: http://marketify.astoundify.com/article/934-page-widgetized
* New: Show blog author on "Recent Blog Posts" widget in "Grid" format.
* Fix: Single blog post byline above mobile menu.
* Fix: Featured Image Background full height on minimal page template.
* Fix: Better support for EDD Wishlists and EDD Favorites.
* Fix: Popular Downloads slider on archive/search pages.
* Fix: Hide comment count when comments are closed.

= 2.3.1: January 13, 2015 =

* Fix: Error on minimal page template when no featured image is enabled.
* Fix: Correct URL on loading assetes in integrations.
* Fix: Invalid HTML in archive-download.php template file.
* Fix: Remove invalid sanitize_callback on customizer checkboxes.
* Fix: Placeholder color in Chrome and Safari.
* Fix: Featured & Popular slider positioning when using RTL.
* Fix: Gravity Forms styles.

= 2.3.0: January 7, 2015 =

* New: Featured Image backgrounds on Minimal page template.
* New: Blog layout and style updates.
* Fix: Download widths in Easy Digital Downloads 2.5+
* Fix: Hide (x) close in page header.
* Fix: Check for menu tasks being registered when setting FES icons.
* Fix: Use a full width row when purchase count is hidden in the product details.
* Fix: Hide hover when team images have no social accounts.
* Fix: Gallery image navigation styles.

= 2.2.1: December 10, 2015 =

* Fix: Build script created invalid style.css file.

= 2.2.0: December 9, 2015 =

* New: Filters for custom menu icons: `marketify_nav_menu_cart_icon_left` and `marketify_nav_menu_search_icon_left`
* New: Use a popup gallery for navigating images on a standard download.
* Fix: Respect widget settings for hiding purchase count.
* Fix: Recaptcha size on vendor contact form (FES).
* Fix: Single download details button spacing.
* Fix: Use "Site Logo" string instead of "Header Image".
* Fix: Search form overlay on single download pages.
* Fix: Scroll only one slide on individual testimonials when only one is showing.

= 2.1.0: November 27, 2015 =

* New: Helpful notices when a widget is placed in the wrong widget area.
* New: Update for future Frontend Submissions submission form compatibility.
* New: Hide "Popular in X" automatically when sorting results.
* Fix: Correct "Author Since" date on Vendor Profile pages.
* Fix: Blog avatar showing the correct user.
* Fix: Love It heart icon.

= 2.0.0: November 20, 2015 =

Version 2.0.0 of Marketify is a total rewrite of the theme. Please do not update directly on your production server.
You should always test the update on a staging server first.

Please thoroughly review: http://marketify.astoundify.com/article/888-upgrading-to-marketify-2-0-0

This update brings both functionality and visual changes. Marketify has been refocused on being a digital marketplace
with extraneous functionalities being deprecated or removed.

* New: Setup Guide to help you get going within minutes.
* New: Style updates including an updated primary menu with a more flexible responsive menu.
       Updated Icon pack to Ionicons which includes hundreds of new icons. http://ionicons.com/
       More consistent and flexible styling throughout the theme.
* New: Individually control the featured areas of standard, audio, and video downloads.
* New: Full support for FacetWP.
* New: Share your downloads, posts, and pages with Jetpack: http://marketify.astoundify.com/article/787-download-single-share
* New: Choose with image upload to use as the grid image automatically in your submission form.
* New: Three separate footer column widget areas.
* New: Widgetized vendor sidebar for Frontend Submissions.
* New: Full Frontend Submissions 2.3+ support.
* New: Ability to adjust /download/ slug permalinks based on customized labels.
* New: Use WordPress' core audio player to improve speed and reduce assets.
* New: Rewrite of all responsive modules.
* Fix: Hundreds of stability improvements and code hardening. Reviewed by Justin Tadlock of ThemeReview.co
* Deprecated: Frontend Submissions Product Details. Replaced with http://marketify.astoundify.com/article/889-download-single-meta
* Deprecated: Soliloquy Slider support. Replaced with http://marketify.astoundify.com/article/777-home-feature-callout
* Deprecated: Custom bbPress styles.
* Deprecated: Custom user contact methods.
* Removed: Projects by WooThemes Support

= 1.2.5: January 20, 2015 =

* New: Add Envato WordPress Toolkit to TMGPA
* Fix: Make sure taxonomy archives respect the selected terms.
* Fix: Always get the current author in the grid.
* Fix: Respect the shortcode count for the Features widget.
* Fix: Make sure self-hosted videos can embed properly.

= 1.2.4.1: January 13, 2015 =

* Fix: Make sure the blog page respects its set featured image.

= 1.2.4: January 12, 2015 = 

* Fix: Allow pages with default shortcodes to be overwritten by providing their own in the page content.
* Fix: Better checking for page custom headers (including vendor page template).
* Fix: Make sure parameters in all shortcodes are respected.
* Fix: Bullets in EDD Taxonomy children widget.

= 1.2.3.2: December 17, 2014 =

* Fix: Add support for updated FES vendor URLs

= 1.2.3.1: December 17, 2014 =

* New: Add support for quantity forms on purchase buttons in EDD 2.2.
* Fix: Make sure sorting when on search results works properly.
* Fix: Don't stretch images in Flexslider.
* Fix: Avoid overly caching download details widget to avoid stale information.

= 1.2.3: October 20, 2014 =

* New: [downloads] shortcode required on "Likes" page template.
* New: Hooks in vendor profile page template to output custom information
* Fix: Cache download count for vendor profiles
* Fix: Love It pagination not working
* Fix: Load the real excerpt and not the post content
* Fix: More reliable self hosted videos for Video format
* Fix: Improve wish list page display
* Fix: Improve FES FPD widget title display
* Fix: Allow grid image size to be set in customizer
* Tweak: Reduce the height of page headers

= 1.2.2.1: August 20, 2014 =

* Fix: Fix pagination positioning on certain pages
* Fix: Updated Earnings icon in FES dashboard
* Fix: Don't escape vendor description/bio which caused the output of HTML tags
* Fix: Make sure the proper classes are still assigned to download grid items
* Fix: Allow the Shop page template to inert its own content

= 1.2.2: July 24, 2014 =

* New: Masonry/stackable grids
* Fix: Respect search for products when on product pages.
* Fix: Better gallery/product image stability with recent FES fixes.
* Fix: Responsive fixes for homepage taxonomy widget.

= 1.2.1.2: July 5, 2014 =

* Fix: Always show the product slider/grid if there are any attached images that aren't also featured.
* Fix: Sorting on shop page template
* Fix: Don't show vendor contact form if viewing your own profile.

= 1.2.1.1: June 25, 2014 =

* Fix: If there is only a featured image and it's not attached to the parent, still show it.
* Fix: Make sure JS settings are always passed to the Testimonials widget

= 1.2.1: June 12, 2014 =

* New: Add support for [edd_register] shortcode.
* Fix: Don't cut off elements when setting equal heights.
* Fix: Load the full size cover image for vendor stores.
* Fix: Improve nested comment styling on mobile.
* Fix: Improve the stability of gallery and featured image output on downloads.
* Fix: Various CSS tweaks and improvements.

= 1.2.0: May 21, 2014 =

* Note: This is a fairly major update. There should be no backwards compatibility issues but it is always important to test on in a development environment before updating your production website.

* New: Projects by WooThemes support.
* New: Product page layout using inline previews moves buy now/action buttons to "Product Details" widget.
* New: Recent Blog Posts widget can be styled like other grid items.
* New: Manually set Audio Previews using FES and a meta key of `preview_files`
* New: Output ratings breakdown in widget if available.
* New: Homepage taxonomy widget to display tags or categories in a "styled" way.
* New: Add a "Description" field to some homepage widgets.
* New: Images for the "Audio" post format are output under the audio player.
* New: Author profiles can now have a header background image when using FES add an upload fields to the vendor profile with the meta key of `cover_image`.
* Fix: Soliloquy 2.0.0+ compatibility. Requires Soliloquy 2.0.0+ to continue using widget.
* Fix: FES 2.2.0+ compatibility. Requires FES 2.2.0+ to continue using.
* Fix: Make sure ratings schema is properly output
* Fix: Only output the first audio file for audio previews in the grid to improve load times.
* Fix: Truncate titles longer than one line (optional in "Appearance > Customize")
* Fix: Hide comments title when comments are disabled.
* Fix: Make the Frontend Submissions menu responsive.
* Fix: Sorting on search results is now accurate.

= 1.1.1: February 20, 2014 =

* New: EDD Wish List support.
* Fix: Love It Pro heart styling.
* Fix: Make sure the demo link is properly centered when using custom prices.
* Fix: Make sure minimal page template has readable text in certain areas.
* Fix: Allow hover to be touched on touch devices.
* Fix: Make sure Shop and Popular page templates can be used on static homepages and just work better in general.

= 1.1.0: February 4, 2014 =

* New: Alternate single product view.
* New: Sorting widget for Download Archive widget area
* New: Second Homepage design that includes a large search bar.
* New: Download info can be forced to show on all grid items.
* New: "Curated Downloads" widget to show specific downloads.
* New: "Recent Posts" widget to show posts from the blog on the homepage.
* New: Send an email directly to a vendor from their profile page.
* New: Allow audio downloads to have a featured background header image.
* New: Default image placeholder when no grid image is set.
* New: Allow product info on grid items to be be toggled on/off/auto.
* Fix: Don't force the Features widget image size.
* Fix: Use Download Archive widget area on Popular Items page template.
* Fix: Make sure the author archives use the proper EDD_SLUG.
* Fix: "Shop" page template so downloads appear properly.

= 1.0.3: January 23, 2014 =

* New: "Shop" Page Template so the standard archives can be set as the homepage.
* New: Add styling support for Custom Prices extension.
* Fix: Make sure searching results in the proper results depending on location.
* Fix: Hide star ratings when replying to a review (as they are not needed).
* Fix: Make sure the pause button on the audio preview displays on mobile.
* Fix: Avoid duplicate hook names to avoid duplicate output of content.
* Fix: Maintain compatability with Features by WooThemes
* Fix: Various responsive tweaks.

= 1.0.2: January 17, 2014 =

* New: "Real" pagination instead of arrows.
* Fix: Maintain compatibility with Features by WooThemes
* Fix: iPad/tablet responsive breakpoints.
* Fix: IE11 display bugs.
* Fix: Make the [downloads] shortcode respond and act like the /downloads/ archive.

= 1.0.1: January 13, 2014 =

* New: Preview audio files directly from the download grid.
* New: Styling support for Mailbag subscription plugin.
* Fix: [downloads] shortcode title formatting.
* Fix: Don't use the old script font for the default logo.
* Fix: Make sure the header search displays correctly in Firefox.
* Fix: Make sure searching in the header searches for downloads on the homepage.
* Fix: When using the "Light" footer style make sure the default link color is dark.
* Fix: Make sure the "Love It" heart is displayed in the correct spot when you have not loved.
* Fix: Make sure action buttons are not blurred in Chrome on Windows.
* Fix: Avoid "jumping" when loading download archive sliders.
* Fix: bbPress forum home search box alignment.

= 1.0: January 8, 2014 =

First release!
