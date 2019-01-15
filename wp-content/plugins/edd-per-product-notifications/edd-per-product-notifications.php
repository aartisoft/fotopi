<?php
/*
Plugin Name: Easy Digital Downloads - Per Product Notifications
Plugin URI: http://markusdrubba.de
Description: Integrates per product sale notification. Send sale notifications to each defined recipient. 
Author: Markus Drubba
Author URI: http://markusdrubba.de
Version: 1.2.3

Easy Digital Downloads is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or 
any later version.

Easy Digital Downloads is distributed in the hope that it will be useful, 
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Easy Digital Downloads. If not, see <http://www.gnu.org/licenses/>.
*/


define( 'DRUBBAPPN_STORE_API_URL', 'https://easydigitaldownloads.com' );
define( 'DRUBBAPPN_PRODUCT_NAME', 'Per Product Notifications' );
define( 'DRUBBAPPN_DIR', plugin_dir_path( __FILE__ ) );

/*
|--------------------------------------------------------------------------
| LICENCE / UPDATING
|--------------------------------------------------------------------------
*/

if( ! class_exists( 'EDD_License' ) ) {
	include( dirname( __FILE__ ) . '/EDD_License_Handler.php' );
}
$eddppn_license = new EDD_License( __FILE__, DRUBBAPPN_PRODUCT_NAME, '1.2.3', 'Markus Drubba' );

/*
|--------------------------------------------------------------------------
| INCLUDES
|--------------------------------------------------------------------------
*/

include_once( DRUBBAPPN_DIR . 'includes/register-settings.php' );
include_once( DRUBBAPPN_DIR . 'includes/meta-box.php' );
include_once( DRUBBAPPN_DIR . 'includes/admin-functions.php' );
