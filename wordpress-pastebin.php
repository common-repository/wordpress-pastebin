<?php
/*
    Copyright 2010 Nicolas Kuttler (email : wp@nicolaskuttler.de )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA

Plugin Name: WordPress Pastebin
Author: Nicolas Kuttler
Author URI: http://www.nkuttler.de/
Plugin URI: http://www.nkuttler.de/wordpress/wordpress-pastebin/
Description: Use your own blog for your own code pastes!
Version: 0.4.5.4
Text Domain: wordpress-pastebin
*/

/**
 * @package wordpress-pastebin
 * @subpackage pluginwrapper
 * @since 0.0.1
 */
if ( !class_exists( 'WordPressPastebin' ) ) {

	class WordPressPastebin {

		/**
		 * Array containing the options
		 *
		 * @since 0.0.1
		 * @var array
		 */
		var $options;

		/**
		 * Path to the plugin
		 *
		 * @since 0.2.1
		 * @var string
		 */
		public $plugin_dir;

		/**
		 * Path to the plugin file
		 *
		 * @since 0.2.1
		 * @var string
		 */
		public $plugin_file;

		/**
		 * PHP4-style constructor
		 *
		 * @return none
		 * @since unknoen
		 */
		function WordPressPastebin() {
			$this->__construct();
		}

		/**
		 * Build the object instance
		 *
		 * @return none
		 * @since 0.0.1
		 */
		function __construct () {
			$this->options = get_option( 'wordpress-pastebin' );
			add_action(
				'init',
				array( &$this, 'register_post_type' )
			);
			// Full path to main file
			$this->plugin_file= __FILE__;
			$this->plugin_dir = dirname( $this->plugin_file );
		}

		/**
		 * Return a specific option value
		 *
		 * @since 0.0.1
		 *
		 * @param string $option name of option to return
		 * @return mixed mixed option or boolean false if option doesn't exist
		 */
		function get_option( $option ) {
			if ( isset ( $this->options[$option] ) )
				return $this->options[$option];
			else
				return false;
		}

		/**
		 * Deactivate this plugin and die when something goes wrong
		 *
		 * @since 0.2
		 *
		 * @param string error
		 * @return none
		 */
		function deactivate_and_die ( $error = false ) {
			load_plugin_textdomain(
				'wordpress-pastebin',
				false,
				basename( $this->plugin_dir ) . '/translations'
			);
			$message = sprintf ( __( "WordPress Pastebin has been automatically deactivated because of the following error: <strong>%s</strong>." ) , $error );
			if ( ! function_exists ( 'deactivate_plugins' ) )
				include ( ABSPATH . 'wp-admin/includes/plugin.php' );
			deactivate_plugins ( __FILE__ );
			wp_die ( $message );
		}

		/**
		 * Return the post type name
		 *
		 * @since 0.2.2
		 *
		 * @return string post type name
		 */
		function get_post_type_name() {
			$name = 'nk_paste';
			if ( defined( 'WORDPRESS_PASTEBIN_POST_TYPE_NAME' ) )
				$name = WORDPRESS_PASTEBIN_POST_TYPE_NAME;
			return $name;
		}

		/**
		 * Return the post type slug
		 *
		 * @since 0.2.2
		 *
		 * @return string post type slug
		 */
		function get_post_type_slug() {
			$slug = 'paste';
			if ( defined( 'WORDPRESS_PASTEBIN_POST_TYPE_SLUG' ) )
				$slug = WORDPRESS_PASTEBIN_POST_TYPE_SLUG;
			return $slug;
		}

		/**
		 * Set up the paste post type
		 *
		 * @since 0.0.1
		 *
		 * @return none
		 */
		function register_post_type() {
			if ( post_type_exists( $this->get_post_type_name() ) ) {
				$this->deactivate_and_die(
					sprintf(
						__( 'The post type %s already exists. Please see the readme.txt for information on how to rename it.', 'wordpress-pastebin' ),
						$this->get_post_type_name()
					)
				);
			}
			// Check which features pastes are configured to support
			$supports	= array( null );
			$taxonomies	= array(
				'post_tag'
			);
			if ( $this->get_option( 'title' ) )
				array_push( $supports, 'title' );
			if ( $this->get_option( 'comments' ) )
				array_push( $supports, 'comments' );
			if ( $this->get_option( 'cat' ) )
				array_push( $taxonomies, 'category' );
			$labels = array(
				'name'			=> __( 'Pastes', 'wordpress-pastebin' ),
				'singular_name'	=> __( 'Paste', 'wordpress-pastebin' ),
				'add_new_item'	=> __( 'Add New Paste', 'wordpress-pastebin' ),
				'edit_item'		=> __( 'Edit paste', 'wordpress-pastebin' ),
				'new_item'		=> __( 'New paste', 'wordpress-pastebin' ),
				'view_item'		=> __( 'View paste', 'wordpress-pastebin' ),
				'search_items'	=> __( 'Search pastes', 'wordpress-pastebin' ),
				'not_found'		=> __( 'No paste found', 'wordpress-pastebin' ),
				'not_found_in_trash'=> __( 'Nothing found in trash', 'wordpress-pastebin' )
			);
			$args = array(
				'public'			=> true,
				'show_ui'			=> true,
				'_builtin'			=> false,
				'_edit_link'		=> 'post.php?post=%d',
				'capability_type'	=> 'post',
				'hierarchical'		=> false,
				'rewrite' => array(
					'slug'			=> $this->get_post_type_slug(),
					'with_front'	=> false // hmmm, make this an option?
				),
				'query_var'			=> $this->get_post_type_name(),
				'supports'			=> $supports,
				'taxonomies'		=> $taxonomies,
				'labels'			=> $labels
			);
			register_post_type(
				$this->get_post_type_name(),
				$args
			);
		}

		/**
		 * Get available syntax highlight plugins
		 *
		 * @return array available plugins
		 * @since 0.0.6
		 */
		function get_highlighter_list() {
			$highlighters = array();
			if ( function_exists( 'wp_syntax_before_filter' ) )
				array_push( $highlighters, 'wp_syntax' );
			if ( class_exists( 'SyntaxHighlighter' ) )
				array_push( $highlighters, 'syntaxhighlighter' );
			if ( function_exists( 'highlighter_header' ) )
				array_push( $highlighters, 'syntax-highlighter-and-code-prettifier' );
			#if ( class_exists( 'AGSyntaxHighlighter' ) )
			#	array_push( $highlighters, 'syntaxhighlighter-plus' );
			array_push( $highlighters, 'none' );
			return( $highlighters );
		}

	}

	/**
	 * Instantiate the appropriate classes
	 */
	$missing = 'Core plugin files are missing, please reinstall the plugin';
	if ( is_admin() ) {
		if ( @include( 'inc/admin.php' ) )
			$WordPressPastebinAdmin = new WordPressPastebinAdmin;
		else
			WordPressPastebin::deactivate_and_die( $missing );
	}
	else {
		if ( @include( 'inc/frontend.php' ) ) {
			global $WordPressPastebinFrontend;
			$WordPressPastebinFrontend = new WordPressPastebinFrontend;
		}
		else
			WordPressPastebin::deactivate_and_die( $missing );
	}
	if ( @include( 'inc/widget-recent.php' ) ) {
		add_action(
			'widgets_init',
			create_function(
				'',
				'return register_widget("WordPressPastebinWidgetRecent");'
			)
		);
	}
	else
		WordPressPastebin::deactivate_and_die( $missing );

}
