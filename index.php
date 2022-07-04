<?php
/**
 * Plugin Name: Business Profile Extra Fields
 * Plugin URI: https://github.com/jseutens/business-profile-extra-fields/
 * Description: Modifies the Business Profile plugin to include a mobile number field.
 * Version: 0.0.4
 * Author: Johan Seutens
 * Author URI: http://www.aati.be
 * @link - https://gist.github.com/NateWr/b28bb63ba8a73bb14eac
 */

if ( ! defined( 'ABSPATH' ) )
	exit;
/**
 * Add extra fields to Business Profile
 *
 *
 */
 add_filter( 'bpfwp_settings_page', 'prefix_add_settings', 100 ); 
/**
 * Add settings options for a whatsapp number - add the fields
 */
function prefix_add_settings( $sap ) {
	$sap->add_setting(
		'bpfwp-settings',
		'bpfwp-contact',
		'text',
		array(
			'id'     => 'whatsapp',
			'title'  => __( 'Whatsapp', BPFWP_TEXTDOMAIN ),
		)
	);
	// Repeat the above array for further fields
	return $sap;
}

add_filter( 'bpwfp_contact_card_defaults', 'prefix_contact_card_defaults' );
/**
 * Add the mobile number to the defaults - true to show in widget
 */
function prefix_contact_card_defaults( $defaults ) {
	$defaults['show_whatsapp'] = false;
	return $defaults;
}

add_filter( 'bpwfwp_component_callbacks', 'prefix_component_callbacks' );
/**
 * Add to the callbacks and slot in at a certain place
 */
function prefix_component_callbacks( $callbacks ) {
	global $bpfwp_controller;
    
    if ( $bpfwp_controller->settings->get_setting( 'whatsapp' ) ) {
		$callbacks['whatsapp'] = 'prefix_print_whatsapp';
		$new_callbacks = array();
		foreach( $callbacks as $key => $val ) {
			$new_callbacks[$key] = $val;
			// When you find the element you want to place it after,
			// slot it in. This positions the new field where you want it - here it is being placed after the phone field
			if ( $key == 'phone' ) {
				$new_callbacks['whatsapp'] = 'prefix_print_whatsapp';
			}
			$callbacks = $new_callbacks;
	    }
    }
	return $callbacks;
}

/**
 * The callback - Print the whatsapp number
 * 
 */
function prefix_print_whatsapp() {
	// This is the mark up - is the same format as the original but has the number in a clickable link.
	global $bpfwp_controller;
	if ( $bpfwp_controller->display_settings['show_whatsapp'] ) : ?>

	<div class="bp-whatsapp" itemprop="telephone">
		
	<a href="https://wa.me/<?php echo (str_replace(' ','',$bpfwp_controller->settings->get_setting('whatsapp') )); ?>"><?php echo $bpfwp_controller->settings->get_setting( 'whatsapp' ); ?></a>
	</div>

	<?php else : ?>
	<meta itemprop="telephone" content="<?php echo esc_attr( $bpfwp_controller->settings->get_setting( 'whatsapp' ) ); ?>">

	<?php endif;
    }
    
add_action( 'wp_enqueue_scripts', 'prefix_added_styles' );
/**
 * Add the mobile icon with a bit of CSS and add via wp_add_inline_style to append to core Business Profile CSS - bpfwp-default
 */
function prefix_added_styles() {
	wp_enqueue_style(
		'bpfwp-default',
		plugins_url() . '/business-profile/assets/css/contact-card.css'
	);
        $custom_css = '
		.bp-whatsapp:before {
				content: "fa-whatsapp";
				font-family: "fontawesome";
			}';
        wp_add_inline_style( 'bpfwp-default', $custom_css );
}
