edd-per-product-notifications
=============================
== Installation ==

 * Unzip the files and upload the folder into your plugins folder (wp-content/plugins/)
 * Activate the plugin in your WordPress admin area.

== Configuration ==

 * Navigate to Downloads > Settings
 * Click on the tab labeled "Extensions"
 * Find the settings area with heading of "Per Product Notification Settings"
 * Configure these settings

== Changelog ==

= 2016-08-19  1.2.3 =
* fixed security issue in save_post action
* fixed wrong condition before updating email post meta

= 2014-03-10  1.2.2 =
* added support for sending notifications for pre-authorized payments

= 2014-01-17  1.2.1 =
* fixed a bug where php errors are thrown when no notification email is set
* fixed a bug where only one notification is send if the buyer bought more than one product

= 2013-12-31  1.2.0 =
* notification emails are now the same like the custom admin notification emails

= 2013-10-23  1.1.1 =
* fixed a bug where crowdfunding notifications are only send if another custom notification is set

= 2013-10-23  1.1.0 =

* added functionality for sending Notifications to Crowdfunding Authors. It is working with Crowdfunding by Astoundify (http://wordpress.org/plugins/appthemer-crowdfunding/)
* using EDD_License_Handler and EDD_SL_Plugin_Updater classes (after updating the plugin, you need to set your licence key again)
* Extension Options are moved from "Email"-Tab to "Extension"-Tab

= 2013-05-23  1.0.0 =

* Initial release