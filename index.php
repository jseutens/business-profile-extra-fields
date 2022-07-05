<?php
/**
 * Plugin Name: Business Profile Extra Fields
 * Plugin URI: https://github.com/jseutens/business-profile-extra-fields/
 * Description: Modifies the Business Profile plugin to include a mobile number , whatsapp and fax number field.
 * Version: 1.0.0
 * Author: Johan Seutens
 * Author URI: http://www.aati.be
 * @link - https://gist.github.com/NateWr/b28bb63ba8a73bb14eac
 */

if ( ! defined( 'ABSPATH' ) )
	exit;
/**
 * remove the contact-card.css style card as it is loaded in the page and not the header
 */
add_action( 'wp_enqueue_scripts', 'remove_bpfwpcontactcard_stylesheet', 20 );
function remove_bpfwpcontactcard_stylesheet() {
    wp_dequeue_style( 'bpfwp-default' );
    wp_deregister_style( 'bpfwp-default' );
}
/**
 * Add extra fields to Business Profile
 *
 *
 */
 add_filter( 'bpfwp_settings_page', 'prefix_add_settings', 100 ); 
/**
 * Add settings options for a cellphone ,whatsapp and fax number - add the fields
 */
function prefix_add_settings( $sap ) {
	$sap->add_setting(
		'bpfwp-settings',
		'bpfwp-contact',
		'text',
		array(
			'id'     => 'cellphone',
			'title'  => __( 'Cellphone', BPFWP_TEXTDOMAIN ),
		)
	);
	$sap->add_setting(
		'bpfwp-settings',
		'bpfwp-contact',
		'text',
		array(
			'id'     => 'whatsapp',
			'title'  => __( 'Whatsapp', BPFWP_TEXTDOMAIN ),
		)
	);
	$sap->add_setting(
		'bpfwp-settings',
		'bpfwp-contact',
		'text',
		array(
			'id'     => 'fax',
			'title'  => __( 'Fax', BPFWP_TEXTDOMAIN ),
		)
	);
	// Repeat the above array for further fields
	return $sap;
}

add_filter( 'bpwfp_contact_card_defaults', 'prefix_contact_card_defaults' );
/**
 * Add the cellphone , whatsapp and fax number to the defaults - true to show in widget
 */
function prefix_contact_card_defaults( $defaults ) {
	$defaults['show_cellphone'] = false;
	$defaults['show_whatsapp'] = false;
    $defaults['show_whatsapp_short'] = false;
	$defaults['show_fax'] = false;
	return $defaults;
}

add_filter( 'bpwfwp_component_callbacks', 'prefix_component_callbacks' );
/**
 * Add to the callbacks and slot in at a certain place
 */
function prefix_component_callbacks( $callbacks ) {
	global $bpfwp_controller;
    
    if ( $bpfwp_controller->settings->get_setting( 'cellphone' ) ) {
		$callbacks['cellphone'] = 'prefix_print_cellphone';
		$new_callbacks = array();
		foreach( $callbacks as $key => $val ) {
			$new_callbacks[$key] = $val;
			// When you find the element you want to place it after,
			// slot it in. This positions the new field where you want it - here it is being placed after the phone field
			if ( $key == 'phone' ) {
				$new_callbacks['cellphone'] = 'prefix_print_cellphone';
			}
			$callbacks = $new_callbacks;
	    }
    }
    if ( $bpfwp_controller->settings->get_setting( 'whatsapp' ) ) {
		$callbacks['whatsapp'] = 'prefix_print_whatsapp';
		$new_callbacks = array();
		foreach( $callbacks as $key => $val ) {
			$new_callbacks[$key] = $val;
			// When you find the element you want to place it after,
			// slot it in. This positions the new field where you want it - here it is being placed after the phone field
			if ( $key == 'cellphone' ) {
				$new_callbacks['whatsapp'] = 'prefix_print_whatsapp';
			}
			$callbacks = $new_callbacks;
	    }
    }
    if ( $bpfwp_controller->settings->get_setting( 'fax' ) ) {
		$callbacks['fax'] = 'prefix_print_fax';
		$new_callbacks = array();
		foreach( $callbacks as $key => $val ) {
			$new_callbacks[$key] = $val;
			// When you find the element you want to place it after,
			// slot it in. This positions the new field where you want it - here it is being placed after the phone field
			if ( $key == 'whatsapp' ) {
				$new_callbacks['fax'] = 'prefix_print_fax';
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
function prefix_print_cellphone() {
	// This is the mark up - is the same format as the original but has the number in a clickable link.
	global $bpfwp_controller;
	if ( $bpfwp_controller->display_settings['show_cellphone'] ) : ?>
	<div class="bp-cellphone" itemprop="telephone">
	<a href="tel:<?php echo (str_replace(' ','',$bpfwp_controller->settings->get_setting('cellphone') )); ?>"><?php echo $bpfwp_controller->settings->get_setting( 'cellphone' ); ?></a>
	</div>
	<?php endif;
    }

/**
 * The callback - Print the whatsapp number
 * 
 */
    function prefix_print_whatsapp() {
	// This is the mark up - is the same format as the original but has the number in a clickable link.
	global $bpfwp_controller;
	
    if ( $bpfwp_controller->display_settings['show_whatsapp_short'] ) : ?>
<a class="bp-whatsapp-short dashicons-before dashicons-whatsapp" href="https://wa.me/<?php echo (str_replace(' ','',$bpfwp_controller->settings->get_setting('whatsapp') )); ?>">&nbsp;</a>
	<?php endif;
	
	if ( $bpfwp_controller->display_settings['show_whatsapp'] ) :  ?>
	<div class="bp-whatsapp dashicons-before dashicons-whatsapp" itemprop="telephone">
	<a href="https://wa.me/<?php echo (str_replace(' ','',$bpfwp_controller->settings->get_setting('whatsapp') )); ?>"><?php echo $bpfwp_controller->settings->get_setting( 'whatsapp' ); ?></a>
	</div>
	<?php endif;
    }

/**
 * The callback - Print the fax number
 * 
 */
function prefix_print_fax() {
	// This is the mark up - is the same format as the original but has the number in a clickable link.
	global $bpfwp_controller;
	if ( $bpfwp_controller->display_settings['show_fax'] ) : ?>
	<div class="bp-fax" itemprop="telephone">
	<a href="tel:<?php echo (str_replace(' ','',$bpfwp_controller->settings->get_setting('fax') )); ?>"><?php echo $bpfwp_controller->settings->get_setting( 'fax' ); ?></a>
	</div>
	<?php endif;
    }
