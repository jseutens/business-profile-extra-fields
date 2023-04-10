<?php
/**
 * Plugin Name: Business Profile Extra Fields
 * Plugin URI: https://github.com/jseutens/business-profile-extra-fields/
 * Description: Modifies the Business Profile plugin to load the css in the header so it can be overruled include extra fields not available in the main plugin, If the main plugin adds fields , the fields in this plugin will go away.
 * Version: 1.2.9
 * Author: Johan Seutens
 * Author URI: http://www.aati.be
 * @link - https://gist.github.com/NateWr/b28bb63ba8a73bb14eac
 * Text Domain: business-profile-extra-fields
 * Domain Path: /languages/
 */
if ( ! defined( 'ABSPATH' ) )
	exit;
// admin notice in case the main plugin is not installed
function display_admin_notice() {
  // Output the notice
    echo '<div class="error"><p>Business Profile Extra Fields requires the <a href="https://wordpress.org/plugins/business-profile/">Five Star Business Profile and Schema</a> to function properly. Please activate the Five Star Business Profile and Schema Plugin to use Business Profile Extra Fields Plugin.</p></div>';
}
// check if the plugin is installed and active
function bpefwp_active_plugins_contains( $name ) {
    $active_plugins = get_option( 'active_plugins' );
    foreach ( $active_plugins as $plugin_file ) {
        // Check if the plugin directory matches the directory you're looking for
        if ( $plugin_file === $name ) {
          return true;
       }
    }
    return false;
}
// is Five Star Business Profile and Schema is active then only execute our code , else skip and display message
// The plugin exists in the list of active plugins
if ( bpefwp_active_plugins_contains( 'business-profile/business-profile.php' ) ) {

	define( 'BPEFWP_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
	define( 'BPEFWP_PLUGIN_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
	define( 'BPEFWP_PLUGIN_FNAME', plugin_basename( __FILE__ ) );
	define( 'BPEFWP_PLUGIN_DIRNAME', plugin_basename( dirname( __FILE__ ) ) );
	define( 'BPEFWP_VERSION', '1.2.9' );
    define( 'BPEFWP_TEXTDOMAIN', 'business-profile-extra-fields');
	
require('includes/shared/bpefwp_core.php');
require('includes/shared/bpefwp_functions.php');
// load file for overruling days of the week with bug from version 2.30 or later
require('includes/shared/weekdays.php');
require('includes/admin/bpefwp_settings.php');
require('includes/admin/bpefwp_admin_page.php');
// load languages
	function bpefwp_load_textdomain() {
		load_plugin_textdomain(BPEFWP_TEXTDOMAIN,false, BPEFWP_PLUGIN_DIRNAME. '/languages');
	}
	add_action( 'plugins_loaded', 'bpefwp_load_textdomain');
//
/**
 * remove the contact-card.css style card as it is loaded in the page and not the header
 */
add_action( 'wp_enqueue_scripts', 'remove_bpfwpcontactcard_stylesheet', 20 );
function remove_bpfwpcontactcard_stylesheet() {
    wp_dequeue_style( 'bpfwp-default' );
    wp_deregister_style( 'bpfwp-default' );
}
/**
 * load the stylesheet from this plugin in to the header so it can be overruled by custom css or other ways
 */
// this is a copy of the orgininal file
add_action( 'wp_enqueue_scripts', 'bpefwp_add_contactcard_stylesheet' );
function bpefwp_add_contactcard_stylesheet () {
			wp_enqueue_style(
				'bpef-default',
				BPEFWP_PLUGIN_URL . '/assets/css/bpef-style.css',
				null,
				BPEFWP_VERSION,
			);
}
// this is the extra fields css file
add_action( 'wp_enqueue_scripts', 'bpefwp_add_extrafields_stylesheet' );
function bpefwp_add_extrafields_stylesheet () {
			wp_enqueue_style('bpefwp-default',BPEFWP_PLUGIN_URL . '/assets/css/bpefwp-style.css',null,BPEFWP_VERSION,);
}
// do not use cellphone field , it will go away when I use the cell_phone for all sites i manage.
// // no location support , to difficult for the moment to add , should be done with extending class-custom-post-types.php
/**
 * Add extra fields to Business Profile
 *
 *
 */
add_filter( 'bpfwp_settings_page', 'bpefwp_add_settings', 100 ); 
/**
 * Add settings options for a cellphone VAT number facebook , instagram , twitter  - add the fields
 * cellphone , VAT-number , PROF-number , bank-account-number , bank-bicswift-number, facebook-link , instagram-link , twitter-link
 */
function bpefwp_add_settings( $sap ) {
 // add a new tab to display my fields
  	$sap->add_section(
		'bpfwp-settings',
		array(
			'id'    => 'bpfwp-extrafields',
			'title' => __( 'Extra fields Information - ONLY FOR THE MAIN LOCATION', BPEFWP_TEXTDOMAIN ),
			'tab'	=> 'bpfwp-basic'
		)
	);
		$sap->add_setting(
		'bpfwp-settings',
		'bpfwp-extrafields',
		'text',
		array(
			'id'     => 'cellphone',
			'title'  => __( 'Cellphone', BPEFWP_TEXTDOMAIN ),
			'description' => __( 'Do not use this anymore , it is included in the main plugin', BPEFWP_TEXTDOMAIN),
			'args'	=> array(
					'label_for' => 'bpfwp-settings[cellphone]',
					'class' 	=> 'bpfwp-cellphone'
					)
		)
	);
	
			$sap->add_setting(
				'bpfwp-settings',
				'bpfwp-extrafields',
				'text',
				array(
					'id'          => 'VAT-number',
					'title'       => __( 'VAT Number', BPEFWP_TEXTDOMAIN ),
					'description' => __( 'For EU countries this is COUNTRY+10digits (not checked against any database)', BPEFWP_TEXTDOMAIN),
					'placeholder' => 'EU0123456789 or EU 0.123.456.789 , etc , you get it ;-)',
					'args'        => array(
						'label_for' => 'bpfwp-settings[VAT-number]',
						'class'     => 'bpfwp-VAT-number'
					)
				)
			);
			$sap->add_setting(
				'bpfwp-settings',
				'bpfwp-extrafields',
				'text',
				array(
					'id'          => 'PROF-number',
					'title'       => __( 'PROF ID', BPEFWP_TEXTDOMAIN ),
					'description' => __( 'Fill in here your professional id for RIZIV / FEWEB or other official affiliation)', BPEFWP_TEXTDOMAIN),
					'placeholder' => 'RIZIV : 0-12345-67-890  / COMPSY : 123456789',
					'args'        => array(
						'label_for' => 'bpfwp-settings[PROF-number]',
						'class'     => 'bpfwp-PROF-number'
					)
				)
			);
			$sap->add_setting(
				'bpfwp-settings',
				'bpfwp-extrafields',
				'text',
				array(
					'id'          => 'bank-account-number',
					'title'       => __( 'Bank account', BPEFWP_TEXTDOMAIN ),
					'description' => __( 'For EU countries this is COUNTRY+digits (not checked against any database)', BPEFWP_TEXTDOMAIN),
					'placeholder' => 'BE00 1234 5678 9012 or BE00.1234.5678.9012 , etc , you get it ;-)',
					'args'        => array(
						'label_for' => 'bpfwp-settings[bank-account-number]',
						'class'     => 'bpfwp-bank-account-number'
					)
				)
			);		
				$sap->add_setting(
				'bpfwp-settings',
				'bpfwp-extrafields',
				'text',
				array(
					'id'          => 'bank-bicswift-number',
					'title'       => __( 'Bank BIC/SWIFT', BPEFWP_TEXTDOMAIN ),
					'description' => __( 'The banks number', BPEFWP_TEXTDOMAIN),
					'placeholder' => '',
					'args'        => array(
						'label_for' => 'bpfwp-settings[bank-bicswift-number]',
						'class'     => 'bpfwp-bank-bicswift-number'
					)
				)
			);		
	
				$sap->add_setting(
				'bpfwp-settings',
				'bpfwp-extrafields',
				'text',
				array(
					'id'          => 'exception-range',
					'title'       => __( 'Exception range', BPEFWP_TEXTDOMAIN ),
					'description' => __( 'any free text to display above opening hours ', BPEFWP_TEXTDOMAIN),
					'placeholder' => '',
					'args'        => array(
						'label_for' => 'bpfwp-settings[exception-range]',
						'class'     => 'bpfwp-exception-range'
					)
				)
			);	
			
			$sap->add_setting(
				'bpfwp-settings',
				'bpfwp-extrafields',
				'text',
				array(
					'id'          => 'facebook-link',
					'title'       => __( 'Facebook Link', BPEFWP_TEXTDOMAIN ),
					'description' => __( 'Link to your Facebook profile/page', BPEFWP_TEXTDOMAIN),
					'placeholder' => 'https://www.facebook.com/',
					'args'        => array(
						'label_for' => 'bpfwp-settings[facebook-link]',
						'class'     => 'bpfwp-facebook-link'
					)
				)
			);
			$sap->add_setting(
				'bpfwp-settings',
				'bpfwp-extrafields',
				'text',
				array(
					'id'          => 'facebook-link-txt',
					'title'       => __( 'Facebook Link Text', BPEFWP_TEXTDOMAIN ),
					'description' => __( 'Text to display on link', BPEFWP_TEXTDOMAIN),
					'placeholder' => 'Follow us on facebook',
					'args'        => array(
						'label_for' => 'bpfwp-settings[facebook-link-txt]',
						'class'     => 'bpfwp-facebook-link-txt'
					)
				)
			);
			$sap->add_setting(
				'bpfwp-settings',
				'bpfwp-extrafields',
				'text',
				array(
					'id'          => 'instagram-link',
					'title'       => __( 'Instagram Link', BPEFWP_TEXTDOMAIN ),
					'description' => __( 'Link to your Instagram page', BPEFWP_TEXTDOMAIN),
					'placeholder' => 'https://www.instagram.com/',
					'args'        => array(
						'label_for' => 'bpfwp-settings[instagram-link]',
						'class'     => 'bpfwp-instagram-link'
					)
				)
			);
			$sap->add_setting(
				'bpfwp-settings',
				'bpfwp-extrafields',
				'text',
				array(
					'id'          => 'instagram-link-txt',
					'title'       => __( 'Instagram Link Text', BPEFWP_TEXTDOMAIN ),
					'description' => __( 'Text to display on link', BPEFWP_TEXTDOMAIN),
					'placeholder' => 'Follow us on instagram',
					'args'        => array(
						'label_for' => 'bpfwp-settings[instagram-link-txt]',
						'class'     => 'bpfwp-instagram-link-txt'
					)
				)
			);
			$sap->add_setting(
				'bpfwp-settings',
				'bpfwp-extrafields',
				'text',
				array(
					'id'          => 'twitter-link',
					'title'       => __( 'Twitter Link', BPEFWP_TEXTDOMAIN ),
					'description' => __( 'Link to your Twitter handle', BPEFWP_TEXTDOMAIN),
					'placeholder' => 'https://twitter.com/',
					'args'        => array(
						'label_for' => 'bpfwp-settings[twitter-link]',
						'class'     => 'bpfwp-twitter-link'
					)
				)
			);
			$sap->add_setting(
				'bpfwp-settings',
				'bpfwp-extrafields',
				'text',
				array(
					'id'          => 'twitter-link-txt',
					'title'       => __( 'Twitter Link Text', BPEFWP_TEXTDOMAIN ),
					'description' => __( 'Text to display on link', BPEFWP_TEXTDOMAIN),
					'placeholder' => 'Follow us on Twitter',
					'args'        => array(
						'label_for' => 'bpfwp-settings[twitter-link-txt]',
						'class'     => 'bpfwp-twitter-link-txt'
					)
				)
			);
			
			
	// Repeat the above array for further fields
	return $sap;
}
add_filter( 'bpwfwp_component_callbacks', 'bpefwp_component_callbacks' );
/**
 * Add to the callbacks and slot in at a certain place
 */
function bpefwp_component_callbacks( $callbacks ) {
	global $bpfwp_controller;  
    if ( $bpfwp_controller->settings->get_setting( 'cellphone' ) ) {
		$callbacks['cellphone'] = 'bpefwp_print_cellphone';
		$new_callbacks = array();
		foreach( $callbacks as $key => $val ) {
			$new_callbacks[$key] = $val;
			// When you find the element you want to place it after,
			// slot it in. This positions the new field where you want it - here it is being placed after the cell_phone field
			if ( $key == 'phone' ) {
				$new_callbacks['cellphone'] = 'bpefwp_print_cellphone';
			}
			$callbacks = $new_callbacks;
	    }
    }
    if ( $bpfwp_controller->settings->get_setting( 'VAT-number' ) ) {
		$callbacks['VAT-number'] = 'bpefwp_print_VAT_number';
		$new_callbacks = array();
		foreach( $callbacks as $key => $val ) {
			$new_callbacks[$key] = $val;
			if ( $key == 'fax_phone' ) {
				$new_callbacks['VAT-number'] = 'bpefwp_print_VAT_number';
			}
			$callbacks = $new_callbacks;
	    }
    }
    if ( $bpfwp_controller->settings->get_setting( 'PROF-number' ) ) {
		$callbacks['PROF-number'] = 'bpefwp_print_PROF_number';
		$new_callbacks = array();
		foreach( $callbacks as $key => $val ) {
			$new_callbacks[$key] = $val;
			if ( $key == 'VAT-number' ) {
				$new_callbacks['PROF-number'] = 'bpefwp_print_PROF_number';
			}
			$callbacks = $new_callbacks;
	    }
    }
    if ( $bpfwp_controller->settings->get_setting( 'bank-account-number' ) ) {
		$callbacks['bank-account-number'] = 'bpefwp_print_bank_account_number';
		$new_callbacks = array();
		foreach( $callbacks as $key => $val ) {
			$new_callbacks[$key] = $val;
			if ( $key == 'PROF-number' ) {
				$new_callbacks['bank-account-number'] = 'bpefwp_print_bank_account_number';
			}
			$callbacks = $new_callbacks;
	    }
    }	
    if ( $bpfwp_controller->settings->get_setting( 'bank-bicswift-number' ) ) {
		$callbacks['bank-bicswift-number'] = 'bpefwp_print_bank_bicswift_number';
		$new_callbacks = array();
		foreach( $callbacks as $key => $val ) {
			$new_callbacks[$key] = $val;
			if ( $key == 'bank-account-number' ) {
				$new_callbacks['bank-bicswift-number'] = 'bpefwp_print_bank_bicswift_number';
			}
			$callbacks = $new_callbacks;
	    }
    }	
    if ( $bpfwp_controller->settings->get_setting( 'facebook-link' ) ) {
		$callbacks['facebook-link'] = 'bpefwp_print_facebook_link';
		$new_callbacks = array();
		foreach( $callbacks as $key => $val ) {
			$new_callbacks[$key] = $val;
			if ( $key == 'bank-bicswift-number' ) {
				$new_callbacks['facebook-link'] = 'bpefwp_print_facebook_link';
			}
			$callbacks = $new_callbacks;
	    }
    }	
    if ( $bpfwp_controller->settings->get_setting( 'facebook-link-txt' ) ) {
		$callbacks['facebook-link-txt'] = 'bpefwp_print_facebook_link_txt';
		$new_callbacks = array();
		foreach( $callbacks as $key => $val ) {
			$new_callbacks[$key] = $val;
			if ( $key == 'facebook-link' ) {
				$new_callbacks['facebook-link-txt'] = 'bpefwp_print_facebook_link_txt';
			}
			$callbacks = $new_callbacks;
	    }
    }	
    if ( $bpfwp_controller->settings->get_setting( 'instagram-link' ) ) {
		$callbacks['instagram-link'] = 'bpefwp_print_instagram_link';
		$new_callbacks = array();
		foreach( $callbacks as $key => $val ) {
			$new_callbacks[$key] = $val;
			if ( $key == 'facebook-link-txt' ) {
				$new_callbacks['instagram-link'] = 'bpefwp_print_instagram_link';
			}
			$callbacks = $new_callbacks;
	    }
    }	
    if ( $bpfwp_controller->settings->get_setting( 'twitter-instagram-txt' ) ) {
		$callbacks['instagram-link-txt'] = 'bpefwp_print_insta_link_txt';
		$new_callbacks = array();
		foreach( $callbacks as $key => $val ) {
			$new_callbacks[$key] = $val;
			if ( $key == 'instagram-link' ) {
				$new_callbacks['instagram-link-txt'] = 'bpefwp_print_insta_link_txt';
			}
			$callbacks = $new_callbacks;
	    }
    }	
    if ( $bpfwp_controller->settings->get_setting( 'twitter-link' ) ) {
		$callbacks['twitter-link'] = 'bpefwp_print_twitter_link';
		$new_callbacks = array();
		foreach( $callbacks as $key => $val ) {
			$new_callbacks[$key] = $val;
			if ( $key == 'instagram-link-txt' ) {
				$new_callbacks['twitter-link'] = 'bpefwp_print_twitter_link';
			}
			$callbacks = $new_callbacks;
	    }
    }	
    if ( $bpfwp_controller->settings->get_setting( 'twitter-link-txt' ) ) {
		$callbacks['twitter-link-txt'] = 'bpefwp_print_twitter_link_txt';
		$new_callbacks = array();
		foreach( $callbacks as $key => $val ) {
			$new_callbacks[$key] = $val;
			if ( $key == 'twitter-link' ) {
				$new_callbacks['twitter-link-txt'] = 'bpefwp_print_twitter_link_txt';
			}
			$callbacks = $new_callbacks;
	    }
    }
    if ( $bpfwp_controller->settings->get_setting( 'exceptions' ) ) {
		$callbacks['exceptions_short'] = 'bpefwp_print_exceptions_short';
		$new_callbacks = array();
		foreach( $callbacks as $key => $val ) {
			$new_callbacks[$key] = $val;
			if ( $key == 'exceptions' ) {
				$new_callbacks['exceptions_short'] = 'bpefwp_print_exceptions_short';
			}
			$callbacks = $new_callbacks;
	    }
    }
    if ( $bpfwp_controller->settings->get_setting( 'exception-range' ) ) {
		$callbacks['exception-range'] = 'bpefwp_print_exception_range';
		$new_callbacks = array();
		foreach( $callbacks as $key => $val ) {
			$new_callbacks[$key] = $val;
			if ( $key == 'exceptions' ) {
				$new_callbacks['exception-range'] = 'bpefwp_print_exception_range';
			}
			$callbacks = $new_callbacks;
	    }
    }
		return $callbacks;
}
/**
 * The callback - Print the cellphone number
 * 
 */
 // bpefwp_print_VAT_number bpefwp_print_PROF_number  bpefwp_print_bank_account_number bpefwp_print_bank_bicswift_number  bpefwp_print_facebook_link bpefwp_print_instagram_link bpefwp_print_twitter_link
 // cellphone , VAT-number , PROF-number , bank-account-number , bank-bicswift-number, facebook-link , instagram-link , twitter-link  
function bpefwp_print_cellphone( $location = false) {
	// This is the mark up - is the same format as the original but has the number in a clickable link.
	global $bpfwp_controller;
	if ( $bpfwp_controller->display_settings['show_cellphone'] ) : ?>
	<div class="bpefwp-cellphone">
	<a href="tel:<?php echo (str_replace(' ','',$bpfwp_controller->settings->get_setting('cellphone') )); ?>"><?php echo $bpfwp_controller->settings->get_setting( 'cellphone' ); ?></a>
	</div>
	<?php endif;
    }
function bpefwp_print_VAT_number( $location = false) {
	global $bpfwp_controller;
	if ( $bpfwp_controller->display_settings['show_VAT_number'] ) : ?>
	<div class="bpefwp-VAT-number">
	<?php echo $bpfwp_controller->settings->get_setting( 'VAT-number' ); ?>
	</div>
	<?php endif;
    }
function bpefwp_print_PROF_number( $location = false) {
	global $bpfwp_controller;
	if ( $bpfwp_controller->display_settings['show_PROF_number'] ) : ?>
	<div class="bpefwp-PROF-number">
	<?php echo $bpfwp_controller->settings->get_setting( 'PROF-number' ); ?>
	</div>
	<?php endif;
    }
function bpefwp_print_bank_account_number( $location = false) {
	global $bpfwp_controller;
	if ( $bpfwp_controller->display_settings['show_bank_account_number'] ) : ?>
	<div class="bpefwp-bank-account-number">
	<?php echo $bpfwp_controller->settings->get_setting( 'bank-account-number' ); ?>
	</div>
	<?php endif;
    }
function bpefwp_print_bank_bicswift_number( $location = false) {
	global $bpfwp_controller;
	if ( $bpfwp_controller->display_settings['show_bank_bicswift_number'] ) : ?>
	<div class="bpefwp-bank-bicswift-number">
	<?php echo $bpfwp_controller->settings->get_setting( 'bank-bicswift-number' ); ?>
	</div>
	<?php endif;
    }
function bpefwp_print_facebook_link( $location = false) {
	global $bpfwp_controller;
	if ( $bpfwp_controller->display_settings['show_facebook_link'] ) : 
	$bpfwp_str_facebook=$bpfwp_controller->settings->get_setting( 'facebook-link' );
	$bpfwp_str_facebook_txt=$bpfwp_controller->settings->get_setting( 'facebook-link-txt' );
	if ($bpfwp_str_facebook_txt=="") {$bpfwp_str_facebook_txt=__('Follow us on Facebook', BPEFWP_TEXTDOMAIN);}
	?>
	<div class="bpefwp-facebook-link">
	<a href="tel:<?php echo $bpfwp_str_facebook ; ?>"><?php echo $bpfwp_str_facebook_txt; ?></a>
	</div>
	<?php endif;
    }
function bpefwp_print_instagram_link( $location = false) {
	global $bpfwp_controller;
	if ( $bpfwp_controller->display_settings['show_instagram_link'] ) : 
	$bpfwp_str_instagram=$bpfwp_controller->settings->get_setting( 'instagram-link' );
	$bpfwp_str_instagram_txt=$bpfwp_controller->settings->get_setting( 'instagram-link-txt' );
	if ($bpfwp_str_instagram_txt=="") {$bpfwp_str_instagram_txt=__('Follow us on Instagram', BPEFWP_TEXTDOMAIN);}
	?>
	<div class="bpefwp-instagram-link">
	<a href="tel:<?php echo $bpfwp_str_instagram ; ?>"><?php echo $bpfwp_str_instagram_txt; ?></a>
	</div>
	<?php endif;
    }
function bpefwp_print_twitter_link( $location = false) {
	global $bpfwp_controller;
	if ( $bpfwp_controller->display_settings['show_twitter_link'] ) : 
	$bpfwp_str_twitter=$bpfwp_controller->settings->get_setting( 'twitter-link' );
	$bpfwp_str_twitter_txt=$bpfwp_controller->settings->get_setting( 'twitter-link-txt' );
	if ($bpfwp_str_twitter_txt=="") {$bpfwp_str_twitter_txt=__('Follow us on Twitter', BPEFWP_TEXTDOMAIN);}
	?>
	<div class="bpefwp-twitter-link">
	<a href="tel:<?php echo $bpfwp_str_twitter ; ?>"><?php echo $bpfwp_str_twitter_txt; ?></a>
	</div>
	<?php endif;
    }
	
function bpefwp_print_exception_range( $location = false) {
	global $bpfwp_controller;
	if ( $bpfwp_controller->display_settings['show_exception_range'] ) { 
	$bpfwp_str_exception_range=$bpfwp_controller->settings->get_setting( 'exception-range' );

       // Output exceptions
        echo '<div class="bp-opening-hours special">';
        echo '<span class="bp-title">' . esc_html(__('Exception days', BPEFWP_TEXTDOMAIN)) . '</span>';
            echo '<div class="bp-date">';
            echo '<span class="label">' . $bpfwp_str_exception_range . '</span>';
            echo '</div>';
        echo '</div>';
    }
}

function bpefwp_print_exceptions_short( $location = false) {
	global $bpfwp_controller;
if ( $bpfwp_controller->display_settings['show_exceptions_short'] ) { 


			$bpefwp_str_exceptions = bpfwp_setting( 'exceptions', $location );
			if ( empty( $bpefwp_str_exceptions ) || ! function_exists( 'wp_date' ) ) {
			return '';
		}
    // Define variables
    $return_data = bpfwp_get_exceptions_array($bpefwp_str_exceptions);
    $date_format = get_option('date_format');
    $time_format = get_option('time_format');
    $tz = new DateTimeZone(wp_timezone_string());
    $data = array( 'exceptions_hours' => array(),);
    // Iterate over exceptions
    //
    foreach ($bpefwp_str_exceptions as $bpefwp_str_exception) {
        if (empty($bpefwp_str_exception['date'])) {
            continue;
        }
        if (time() > strtotime($bpefwp_str_exception['date']) + 24*3600) {
            continue;
        }
        $data['exceptions_hours'][] = $bpefwp_str_exception;
    }

    // Sort exceptions by date
    usort($data['exceptions_hours'], function ($a, $b) {
        return strcasecmp($a['date'], $b['date']);
    });

    // Check if there are any exceptions
    if (count($data['exceptions_hours']) > 0) {
        // Output exceptions
        echo '<div class="bp-opening-hours special">';
        echo '<span class="bp-title">' . esc_html(__('Exception days', BPEFWP_TEXTDOMAIN)) . '</span>';
        foreach ($data['exceptions_hours'] as $exception) {
            $date  = new DateTime($exception['date'], $tz);
            $start = new DateTime($exception['time']['start'], $tz);
            $end   = new DateTime($exception['time']['end'], $tz);
            echo '<div class="bp-date">';
            echo '<span class="label">' . wp_date( $date_format, $date->format( 'U' ) ) . '</span>';
            echo '<span class="bp-times">';
            echo '<span class="bp-time">';
            if (array_key_exists('time', $exception)) {
                // if time print it else print Closed
                $data['exceptions_hours'][] = $time_exception;
                echo date($time_format, $start->format('U')) . ' – ' . date($time_format, $end->format('U'));
            } else {
                echo esc_html(__('Closed', BPEFWP_TEXTDOMAIN));
            }
            echo '</span>';
            echo '</span>';
            echo '</div>';
        }
        echo '</div>';
    }

    // Return data
    return ;
}
}
//
//Shortcodes to display fields without metadata or fixed layout
// name address phone cell_phone whatsapp fax_phone ordering-link contact 
// cellphone , VAT-number , bank-account-number , bank-bicswift-number, facebook-link , instagram-link , twitter-link
// [bpefwp_name] [bpefwp_address] [bpefwp_phone] [bpefwp_cell_phone] [bpefwp_whatsapp] [bpefwp_fax_phone] [bpefwp_ordering_link] [bpefwp_contact] 
//[bpefwp_VAT_number][bpefwp_PROF_number] [bpefwp_bank_account_number] [bpefwp_bank_bicswift_number] [bpefwp_facebook] [bpefwp_instagram] [bpefwp_twitter]
function bpefwp_display_name() {
	global $bpfwp_controller;
	$bpfwp_str_name=$bpfwp_controller->settings->get_setting( 'name' );
	return $bpfwp_str_name;
}
add_shortcode( 'bpefwp_name', 'bpefwp_display_name' );
// shortcode to use [bpefwp_name]
//
function bpefwp_display_address() {
	global $bpfwp_controller;
	$bpfwp_str_address=$bpfwp_controller->settings->get_setting( 'address' );
	$bpfwp_str_address=preg_replace('~\R~u', " - ", $bpfwp_str_address['text']);
	return $bpfwp_str_address;
}
add_shortcode( 'bpefwp_address', 'bpefwp_display_address' );
// shortcode to use [bpefwp_address]
//
function bpefwp_display_phone() {
	global $bpfwp_controller;
	$bpfwp_str_phone=$bpfwp_controller->settings->get_setting( 'phone' );
	return $bpfwp_str_phone;
}
add_shortcode( 'bpefwp_phone', 'bpefwp_display_phone' );
// shortcode to use [bpefwp_phone]
//
function bpefwp_display_cell_phone() {
	global $bpfwp_controller;
	$bpfwp_str_cell_phone=$bpfwp_controller->settings->get_setting( 'cell-phone' );
	return $bpfwp_str_cell_phone;
}
add_shortcode( 'bpefwp_cell_phone', 'bpefwp_display_cell_phone' );
// shortcode to use [bpefwp_cell_phone]
//
function bpefwp_display_whatsapp() {
	global $bpfwp_controller;
	$bpfwp_str_whatsapp=$bpfwp_controller->settings->get_setting( 'whatsapp' );
	return $bpfwp_str_whatsapp;
}
add_shortcode( 'bpefwp_whatsapp', 'bpefwp_display_whatsapp' );
// shortcode to use [bpefwp_whatsapp]
//
function bpefwp_display_fax_phone() {
	global $bpfwp_controller;
	$bpfwp_str_fax_phone=$bpfwp_controller->settings->get_setting( 'fax-phone' );
	return $bpfwp_str_fax_phone;
}
add_shortcode( 'bpefwp_fax_phone', 'bpefwp_display_fax_phone' );
// shortcode to use [bpefwp_fax_phone]
//
function bpefwp_display_ordering_link() {
	global $bpfwp_controller;
	$bpfwp_str_ordering_link=$bpfwp_controller->settings->get_setting( 'ordering-link' );
	return $bpfwp_str_ordering_link;
}
add_shortcode( 'bpefwp_ordering_link', 'bpefwp_display_ordering_link' );
// shortcode to use [bpefwp_ordering_link]
//
function bpefwp_display_contact() {
	global $bpfwp_controller;
	$bpfwp_str_contact=$bpfwp_controller->settings->get_setting( 'contact' );
	return $bpfwp_str_contact;
}
add_shortcode( 'bpefwp_contact', 'bpefwp_display_contact' );
// shortcode to use [bpefwp_contact]
//
function bpefwp_display_contact_email() {
	global $bpfwp_controller;
	$bpfwp_str_contact=$bpfwp_controller->settings->get_setting( 'contact-email' );
	return $bpfwp_str_contact;
}
add_shortcode( 'bpefwp_contact_email', 'bpefwp_display_contact_email' );
// shortcode to use [bpefwp_contact_email]
//
function bpefwp_display_cellphone() {
	global $bpfwp_controller;
	$bpfwp_str_cellphone=$bpfwp_controller->settings->get_setting( 'cellphone' );
	return $bpfwp_str_cellphone;
}
add_shortcode( 'bpefwp_cellphone', 'bpefwp_display_cellphone' );
// shortcode to use [bpefwp_cellphone]
//
function bpefwp_display_VAT_number() {
	global $bpfwp_controller;
	$bpfwp_str_VAT_number=$bpfwp_controller->settings->get_setting( 'VAT-number' );
	$bpfwp_str_VAT_number=__('VAT', BPEFWP_TEXTDOMAIN).' : '.$bpfwp_str_VAT_number;
	return $bpfwp_str_VAT_number;
}
add_shortcode( 'bpefwp_VAT_number', 'bpefwp_display_VAT_number' );
// shortcode to use [bpefwp_VAT_number]
// 
function bpefwp_display_PROF_number() {
	global $bpfwp_controller;
	$bpfwp_str_PROF_number=$bpfwp_controller->settings->get_setting( 'PROF-number' );
	$bpfwp_str_PROF_number=__('Professional ID', BPEFWP_TEXTDOMAIN).' : '.$bpfwp_str_PROF_number;
	return $bpfwp_str_PROF_number;
}
add_shortcode( 'bpefwp_PROF_number', 'bpefwp_display_PROF_number' );
// shortcode to use [bpefwp_PROF_number]
// 
function bpefwp_display_bank_account_number() {
	global $bpfwp_controller;
	$bpfwp_str_bank_account_number=$bpfwp_controller->settings->get_setting( 'bank-account-number' );
	$bpfwp_str_bank_account_number=__('IBAN', BPEFWP_TEXTDOMAIN).' : '.$bpfwp_str_bank_account_number;
	return $bpfwp_str_bank_account_number;
}
add_shortcode( 'bpefwp_bank_account_number', 'bpefwp_display_bank_account_number' );
// shortcode to use [bpefwp_bank_account_number]
// 
function bpefwp_display_bank_bicswift_number() {
	global $bpfwp_controller;
	$bpfwp_str_bicswift_number=$bpfwp_controller->settings->get_setting( 'bank-bicswift-number' );
	$bpfwp_str_bicswift_number=__('BIC', BPEFWP_TEXTDOMAIN).' : '.$bpfwp_str_bicswift_number;
	return $bpfwp_str_bicswift_number;
}
add_shortcode( 'bpefwp_bank_bicswift_number', 'bpefwp_display_bank_bicswift_number' );
// shortcode to use [bpefwp_bank_bicswift_number]
// 
function bpefwp_display_facebook() {
	global $bpfwp_controller;
	$bpfwp_str_facebook=$bpfwp_controller->settings->get_setting( 'facebook-link' );
	$bpfwp_str_facebook_txt=$bpfwp_controller->settings->get_setting( 'facebook-link-txt' );
	if ($bpfwp_str_facebook_txt=="") {$bpfwp_str_facebook_txt=__('Follow us on Facebook', BPEFWP_TEXTDOMAIN);}
	$bpfwp_str_facebook="<a href=\"".$bpefwp_str_facebook."\" class=\"bpfwp-sc-facebook-link\">".$bpfwp_str_facebook_txt."</a>";
	return $bpfwp_str_facebook;
}
add_shortcode( 'bpefwp_facebook', 'bpefwp_display_facebook' );
// shortcode to use [bpefwp_facebook]
// 
function bpefwp_display_instagram() {
	global $bpfwp_controller;
	$bpfwp_str_instagram=$bpfwp_controller->settings->get_setting( 'instagram-link' );
	$bpfwp_str_instagram_txt=$bpfwp_controller->settings->get_setting( 'instagram-link-txt' );
	if ($bpfwp_str_instagram_txt=="") {$bpfwp_str_instagram_txt=__('Follow us on Instagram', BPEFWP_TEXTDOMAIN);}
	$bpfwp_str_instagram = "<a href=\"".$bpfwp_str_instagram."\" class=\"bpfwp-sc-instagram-link\">".$bpfwp_str_instagram_txt."</a>";
	return $bpfwp_str_instagram;
}
add_shortcode( 'bpefwp_instagram', 'bpefwp_display_instagram' );
// shortcode to use [bpefwp_instagram]
//
function bpefwp_display_twitter() {
	global $bpfwp_controller;
	$bpfwp_str_twitter=$bpfwp_controller->settings->get_setting( 'twitter-link' );
	$bpfwp_str_twitter_txt=$bpfwp_controller->settings->get_setting( 'twitter-link-txt' );
	if ($bpfwp_str_twitter_txt=="") {$bpfwp_str_twitter_txt=__('Follow us on Twitter', BPEFWP_TEXTDOMAIN);}
	$bpfwp_str_twitter="<a href=\"".$bpefwp_str_twitter."\" class=\"bpfwp-sc-twitter-link\">".$bpfwp_str_twitter_txt."</a>";
	return $bpfwp_str_twitter;
}
add_shortcode( 'bpefwp_twitter', 'bpefwp_display_twitter' );
// shortcode to use [bpefwp_twitter]
//
function bpefwp_display_exception_range() {
    global $bpfwp_controller;
	$bpefwp_str_exception_range =$bpfwp_controller->settings->get_setting( 'exception-range' );
    // Return data
    return $bpefwp_str_exception_range;
}
add_shortcode( 'bpefwp_exception_range', 'bpefwp_display_exception_range' );
// shortcode to use [bpefwp_exception_range]
//
function bpefwp_display_exceptions() {
    global $bpfwp_controller;
			$bpefwp_str_exceptions = bpfwp_setting( 'exceptions', $location );
			if ( empty( $bpefwp_str_exceptions ) || ! function_exists( 'wp_date' ) ) {
			return '';
		}
    // Define variables
    $return_data = bpfwp_get_exceptions_array($bpefwp_str_exceptions);
    $date_format = get_option('date_format');
    $time_format = get_option('time_format');
    $tz = new DateTimeZone(wp_timezone_string());
    $data = array( 'exceptions_hours' => array(),);
    // Iterate over exceptions
    //
    foreach ($bpefwp_str_exceptions as $bpefwp_str_exception) {
        if (empty($bpefwp_str_exception['date'])) {
            continue;
        }
        if (time() > strtotime($bpefwp_str_exception['date']) + 24*3600) {
            continue;
        }
        $data['exceptions_hours'][] = $bpefwp_str_exception;
    }

    // Sort exceptions by date
    usort($data['exceptions_hours'], function ($a, $b) {
        return strcasecmp($a['date'], $b['date']);
    });

    // Check if there are any exceptions
    if (count($data['exceptions_hours']) > 0) {
        // Output exceptions
        echo '<div class="bp-opening-hours special">';
        echo '<span class="bp-title">' . esc_html(__('Exception days', BPEFWP_TEXTDOMAIN)) . '</span>';
        foreach ($data['exceptions_hours'] as $exception) {
            $date  = new DateTime($exception['date'], $tz);
            $start = new DateTime($exception['time']['start'], $tz);
            $end   = new DateTime($exception['time']['end'], $tz);
            echo '<div class="bp-date">';
            echo '<span class="label">' . wp_date( $date_format, $date->format( 'U' ) ) . '</span>';
            echo '<span class="bp-times">';
            echo '<span class="bp-time">';
            if (array_key_exists('time', $exception)) {
                // if time print it else print Closed
                $data['exceptions_hours'][] = $time_exception;
                echo date($time_format, $start->format('U')) . ' – ' . date($time_format, $end->format('U'));
            } else {
                echo esc_html(__('Closed', BPEFWP_TEXTDOMAIN));
            }
            echo '</span>';
            echo '</span>';
            echo '</div>';
        }
        echo '</div>';
    }

    // Return data
    return ;
}
add_shortcode( 'bpefwp_exceptions', 'bpefwp_display_exceptions' );
// shortcode to use [bpefwp_exceptions]
// end of shortcodes
//
add_filter( 'bpwfp_contact_card_defaults', 'bpefwp_contact_card_defaults' );
/**
 * Add all fields to the defaults with value FALSE - true to show in widget
 */
function bpefwp_contact_card_defaults( $defaults ) {
	$defaults['location'] = false;
	$defaults['show_name'] = true;
	$defaults['show_address'] = true;
	$defaults['show_get_directions'] = false;
	$defaults['show_phone'] = true;
	$defaults['show_cellphone'] = false;
	$defaults['show_cell_phone'] = false;
	$defaults['show_whatsapp'] = false;
	$defaults['show_fax'] = false;
	$defaults['show_ordering_link'] = false;
	$defaults['show_contact'] = false;
	$defaults['show_opening_hours'] = false;
	$defaults['show_opening_hours_brief'] = false;
	$defaults['show_map'] = false;
	$defaults['show_image'] = false;
	$defaults['show_VAT_number'] = false;
	$defaults['show_PROF_number'] = false;
	$defaults['show_bank_account_number'] = false;
	$defaults['show_bank_bicswift_number'] = false;
	$defaults['show_facebook_link'] = false;
	$defaults['show_instagram_link'] = false;
	$defaults['show_twitter_link'] = false;
	$defaults['show_exceptions'] = false;
	$defaults['show_exceptions_short'] = false;
	$defaults['show_exception_range'] = false;
	return $defaults;
}
// if plugin is not active do this instead	
} else {
    // The plugin does not exist in the list of active plugins
	// Hook into the admin_notices action to display the notice
add_action( 'admin_notices', 'display_admin_notice' );
}