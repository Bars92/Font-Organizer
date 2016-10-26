<?php
/**
 * @package Font_Organizer
 * @version 1.0.0
 */
/*
Plugin Name: Font Organizer
Plugin URI: https://wordpress.org/plugins/font-organizer/
Description: afsaf.
Author: Me
Version: 1.0.0
Author URI: 
*/

define( 'FO_ABSPATH', plugin_dir_path( __FILE__ ) );
define( 'FO_USABLE_FONTS_DATABASE', 'usable_fonts' );

global $fo_db_version;
$fo_db_version = '1.0.0';

function fo_update_db_check() {
    global $fo_db_version;
    if ( get_site_option( 'fo_db_version' ) != $fo_db_version ) {
        fo_install();
    }
}

add_action( 'plugins_loaded', 'fo_update_db_check' );
register_activation_hook( __FILE__, 'fo_install' );
add_action( 'init', 'fo_init' );

function fo_init(){
	if( is_admin() ){
		add_filter('upload_mimes', 'fo_allow_upload_types');
		include FO_ABSPATH . 'helpers.php';

		include FO_ABSPATH . 'settings.php'; 
	    $settings_page = new FoSettingsPage();
	}
}

function fo_allow_upload_types($existing_mimes = array()){
	$existing_mimes['ttf'] = 'application/octet-stream';
	$existing_mimes['woff'] = 'application/x-font-woff';
	$existing_mimes['woff2'] = 'application/x-font-woff';
	$existing_mimes['otf'] = 'application/x-font-woff';
	
	return $existing_mimes;
}

function fo_install() {
	global $wpdb;
	global $fo_db_version;

	$table_name = $wpdb->prefix . FO_USABLE_FONTS_DATABASE;
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		name varchar(255) NOT NULL,
		url text NOT NULL,
		custom int(1) DEFAULT 0,
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	// Set the db version to current.
	add_option( 'fo_db_version', $fo_db_version );
}

?>