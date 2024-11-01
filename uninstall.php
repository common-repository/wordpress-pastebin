<?php
/**
 * Delete the wordpress-pastebin option
 *
 * @package wordpress-pastebin
 * @subpackage uninstall
 * @since 0.2.4
 */

// If uninstall/delete not called from WordPress then exit
if( ! defined ( 'ABSPATH' ) && ! defined ( 'WP_UNINSTALL_PLUGIN' ) )
	exit ();

// Delete shadowbox option from options table
delete_option ( 'wordpress-pastebin' );
