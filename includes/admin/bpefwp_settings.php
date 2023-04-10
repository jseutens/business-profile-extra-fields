<?php
// Check if the ABSPATH constant is defined
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
//
//
function BPEFWP_add_menu_item() {
    add_menu_page(
        'Business Profile Extra Fields', // The text to be displayed in the title tags of the page when the menu is selected
        'BP Extra fields', // The text to be used for the menu
        'manage_options', // The capability required for this menu to be displayed to the user.
        'BPEFWP-menu', // The slug of the menu item
        'BPEFWP_menu_callback', // The function to be called to output the content for this page
        'dashicons-admin-generic', // The icon for the menu item
        52 // The position in the menu order this item should appear
    );
}
add_action('admin_menu', 'BPEFWP_add_menu_item');

