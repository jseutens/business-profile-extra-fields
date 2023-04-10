=== business-profile-extra-fields ===
Contributors: jseutens
Tags:  mobile , fax , cellphone , whatsapp, business profile, seo, local seo, schema, address, google map, contact, phone, contact card, vcard, contact info, business location, business address, business map, business schema, organization schema, corporation schema, contact schema, address schema, location schema, map schema, business structured data, business microdata, address microdata, location structured data, location microdata, contact shortcode, location shortcode, address shortcode, schema shortcode, gutenberg schema, gutenberg address
Requires at least: 6.0
Tested up to: 6.1.1
Requires PHP: 7.4
Stable tag: 1.2.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==
You need the Five star business profile plugin to use this plugin
https://wordpress.org/plugins/business-profile/

shortcodes
[contact-card]
Will display name / address / phone 

display all
[contact-card show_name=1 show_address=1 show_get_directions=1 show_phone=1 show_phone-formatted=1 show_cellphone=1 show_cell_phone=1  show_fax=1 show_whatsapp=1 show_contact=1 show_opening_hours=1 show_opening_hours_brief=1 show_map=1 show_booking_link=1 show_VAT_number=1 show_PROF_number=1 show_bank_account_number=1 show_bank_bicswift_number=1 show_facebook_link=1 show_instagram_link=1 show_twitter_link=1 show_exceptions=1 show_exceptions_short=0 show_exception_range=0]

display none
[contact-card show_name=0 show_address=0 show_get_directions=0 show_phone=0 show_phone-formatted=0 show_cellphone=0 show_cell_phone=0  show_fax=0 show_whatsapp=0 show_contact=0 show_opening_hours=0 show_opening_hours_brief=0 show_map=0 show_booking_link=0 show_VAT_number=0 show_PROF_number=0 show_bank_account_number=0 show_bank_bicswift_number=0 show_facebook_link=0 show_instagram_link=0 show_twitter_link=0 show_exceptions=0 show_exceptions_short=0 show_exception_range=0]

Change the 1 to a 0 if you don't want to display it  or 0 to 1 to display
Just here to easy copy paste the code

Fields without markup just the value
[bpefwp_name]
[bpefwp_address]
[bpefwp_phone]
[bpefwp_cell_phone]
[bpefwp_whatsapp]
[bpefwp_fax_phone]
[bpefwp_ordering_link]
[bpefwp_contact]
[bpefwp_contact-email]
[bpefwp_VAT_number]
[bpefwp_PROF_number]
[bpefwp_bank_account_number]
[bpefwp_bank_bicswift_number] 
[bpefwp_exceptions]
[bpefwp_exception_range]

Links without extra markup but do have a shortcode class for the link 
[bpefwp_facebook]
[bpefwp_instagram]
[bpefwp_twitter]




== Installation ==
This section describes how to install the plugin and get it working.
You need the Five star plugin to use this plugin
1. Upload the plugin files to the `/wp-content/plugins/business-profile-extra-fields` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress

== Frequently Asked Questions ==

You need the Five star plugin to use this plugin


Kept my cellphone (main plugin uses show_cell_phone)
to display the mobile/cellphone number : show_cellphone=1  
Will be removed in the future

What's up next
including one for just a whatsapp logo whitout numbers
Loads the css from the original plugin instead of a copy in this plugin


== Changelog ==
= 1.2.9 =
* bpfwp_get_opening_hours_array , array bugfix
= 1.2.8 =
* bug fixes
* added contact-email shortcut
= 1.2.7 =
* moved so faq stuff to description
= 1.2.6 =
* added free text field [bpefwp_exceptions_range] , needs extra work or another field with a range that can be included in the normal list.
* added translations
= 1.2.5 =
* bugfixes and added show_exceptions=0 show_exceptions_short=1 , so you can choose between the normal or the short display of special days
= 1.2.4 =
* added shortcode [bpefwp_exceptions]  , displays all days in 1 list 
* standard display exception days from five star is standard setting is off now even with display hours on , can be overruled.
* these updates are just there untill these get the bugs out of their plugin that doens't let you overrule stuiff , so i added code in here.
* displaying the days of the week doesn't work from 2.3.0 on as they are not translated in the translation files included in the plugin , you need to use loco translate to fill thes in and create the language .po yourself
* compatible with 2.2.5
= 1.2.3 =
* Five star moved days of the week translations to the payed plugin or didn't provide the needed translations from version 2.3.0 , added the include file weekdays.php to overrule their weekname settings. will be removed if fixed by Five stars.
= 1.2.2 =
* Added professional ID field
* updated translations
= 1.2.1 =
* Translation fixes and typos.
* added UK language
= 1.2.0 =
* Added text fields for the social links
* Added translations for Belgium (NL-FR-DE) , Netherlands , Germany, France and US
= 1.1.1 =
* Bugfix in admin notice
= 1.1.0 =
* Added fields for VAT-number , bank-account-number , bank-bicswift-number, facebook-link , instagram-link , twitter-link
* Split the css in 2 files , the original and the extra fields
* Added shortcodes without markup or meta data
= 1.0.4 =
* Removed fax and whatsapp as these are now available with the same name in the main plugin
= 1.0.3 =
* Bugfix for the css file , removed 2 !important lines
= 1.0.2 =
* Loading standard css in the header, can be overruled that way in a normal way without !important
* Changed index.php to plugin name 
* Added directories
* Added style sheet
= 1.0.1 =
* WDAC 2.0 compliance for the added fields
= 1.0.0 =
* initial release
== Upgrade Notice ==
upgrade as you please
