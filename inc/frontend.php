<?php
/**
 * Review custom post type
 *
 * @package wordpress-pastebin
 * @subpackage frontend
 * @since 0.0.1
 */
class WordPressPastebinFrontend extends WordPressPastebin {

	/**
	 * PHP4 constructor
	 *
	 * @since 0.0.1
	 */
	function WordPressPastebinFrontend() {
		$this->__construct();
	}

	/**
	 * Set up the post type + frontend
	 *
	 * @since 0.0.1
	 */
	function __construct() {
		WordPressPastebin::__construct();
		add_filter(
			'the_content',
			array( &$this, 'add_tags' ),
			0
		);
		if ( $this->get_option( 'thanks' ) )
			add_filter( 'the_content', array( &$this, 'thanks' ) );
		if ( $this->get_option( 'add_note' ) )
			add_filter( 'the_content', array( &$this, 'add_note' ) );
		// @todo can't we use the visibility helper?
		if ( $this->get_option( 'feed' )  )
			add_action( 'request', array( &$this, 'add_to_feed' ) );
		// @todo option
		add_action( 'pre_get_posts', array( &$this, 'add_visibility_filters' ), 0 );
		if ( $this->get_option( 'frontend_editing' ) ) {
			add_action( 'init', array( &$this, 'parse_forms' ), 10 );
			add_filter(
				'the_content',
				array( &$this, 'add_edit_form' ),
				10
			);
		}
		if ( $this->get_option( 'anonymous_posting' ) ) {
			add_shortcode(
				'wordpress-pastebin',
				array( &$this, 'add_add_form' )
			);
		}
	}

	/**
	 * Add the tags necessary for syntax highlighting. I don't think this can
	 * be done with a filter because at least wp-syntax runs with priority 0.
	 *
	 * @since 0.0.1
	 *
	 * @param string $content paste content
	 * @return string The updated paste content
	 */
	function add_tags( $content ) {
		global $post;
		if ( $post->post_type != $this->get_post_type_name() )
			return $content;
		$highlight		= $this->get_paste_highlight( $post->ID );
		$highlighter	= $this->get_highlighter();
		switch( $highlighter ) {
			case 'wp_syntax':
				$r = "<pre lang=\"$highlight\">$content</pre>";
				break;
			case 'syntaxhighlighter':
				$r = '['."sourcecode language=\"$highlight\"']$content".'[/sourcecode]';
				break;
			case 'syntax-highlighter-and-code-prettifier':
				$r = "<pre class=\"brush:$highlight\">$content</pre>";
				break;
			case 'none':
			default:
				$r = '<pre>' . htmlentities( $content ) . '</pre>';
				break;
		}
		return $r;
	}

	/**
	 * Return a paste's configured highlight language
	 *
	 * @since 0.4.1
	 *
	 * @param ind $id Post ID
	 * @return string Highlight language
	 */
	function get_paste_highlight( $id ) {
		$custom = get_post_custom( $id );
		if ( !isset( $custom['paste-highlight'][0] ) )
			$highlight = '';
		else
			$highlight = $custom['paste-highlight'][0];
		return $highlight;
	}

	/**
	 * Check if a frontend form was submitted
	 *
	 * Update or create new pastes, shorten their name and redirect to the
	 * (updated) paste.
	 *
	 * @since 0.4
	 *
	 * @return none
	 */
	function parse_forms() {
		$nonce = @$_POST['_wpnonce'];
		// udpate paste
		if ( @$_POST['wordpress-pastebin'] ) {
			if ( !current_user_can( 'edit_posts' ) )
				return;
			if ( !wp_verify_nonce( $nonce, 'update-paste' ) )
				die( __( 'Security check', 'wordpress-pastebin' ) );
			$mypost = get_post( $_POST['ID'] );
			if ( !$mypost )
				return;
			$mypost->post_content = $_POST['wordpress-pastebin'];
			// don't update the existing post @todo option
			unset( $mypost->ID );
			// create the updated paste
			$id = wp_insert_post( $mypost );
			// preserve highlight info @todo editable..
			add_post_meta(
				$id,
				'paste-highlight',
				$this->get_paste_highlight( $_POST['ID'] )
			);
			$this->short_name( $id );
			$this->redirect( $id );
		}
		// new paste
		elseif ( @$_POST['wordpress-insert-pastebin'] ) {
			if ( !wp_verify_nonce( $nonce, 'insert-paste' ) )
				die( __( 'Security check', 'wordpress-pastebin' ) );
			$mypost = array(
				'post_title'	=> @$_POST['post_title'],
				'post_content'	=> stripslashes(
					$_POST['wordpress-insert-pastebin']
				),
				'post_status'	=> 'publish',
				'post_type'		=> $this->get_post_type_name()
			);
			$id = wp_insert_post( $mypost );
			add_post_meta( $id, 'paste-highlight', @$_POST['paste-highlight'] );
			$this->short_name( $id );
			$this->redirect( $id );
		}
	}

	/**
	 * Redirect to a paste
	 *
	 * @since 0.4.1
	 *
	 * @param int $id Post ID
	 */
	function redirect( $id ) {
		$url = get_permalink( $id );
		header( "Location: $url", TRUE, 302 );
	}

	/**
	 * Shorten a post's name
	 *
	 * @since 0.4.1
	 *
	 * @param int $id Post ID
	 * @return int Post ID
	 */
	function short_name( $id ) {
		// shorten the name of the new post and update the post
		$name	= base_convert( $id, 10, 36 );
		$id		= wp_update_post( array(
			'ID'		=> $id,
			'post_name'	=> $name
		) );
		return $id;
	}

	/**
	 * The add paste frontend form
	 *
	 * @since 0.4.1
	 *
	 * @return none
	 */
	function add_add_form() {
		if ( @include( 'admin.php' ) )
			$WordPressPastebinAdmin = new WordPressPastebinAdmin;
		else
			WordPressPastebin::deactivate_and_die( $missing );
		$nonce = wp_create_nonce ( 'insert-paste' );
		$r = '<form id="insert_paste" action="" method="post">';
		if ( $this->get_option( 'title' ) )
			$r .= '<input type="text" name="post_title" value="title" />';
		$r .= '<input type="hidden" name="_wpnonce" value="' . $nonce . '" />';
		$r .= '<textarea cols="80" rows="25" name="wordpress-insert-pastebin">';
		$r .= '</textarea>';
		$r .= '<select name="paste-highlight">';
		foreach( $WordPressPastebinAdmin->languages() as $lang )
			$r .= "<option >$lang</option>";
		$r .= '</select>';
		$r .= '<br /><input type="submit" value="' .
			__( 'Create paste', 'wordpress-pastebin' ) . '">';
		$r .= '</form>';
		echo $r;
	}

	/**
	 * Add the edit form if the user is authorized
	 *
	 * @since 0.4
	 *
	 * @param string $content post content
	 * @return none
	 */
	function add_edit_form( $content ) {
		// @todo option
		if ( !current_user_can( 'edit_posts' ) )
			return $content;
		global $post;
		if ( $post->post_type != $this->get_post_type_name() )
			return $content;
		$nonce = wp_create_nonce ( 'update-paste' );
		$r = '<form id="update_paste" action="" method="post">';
		$r .= '<input type="hidden" name="_wpnonce" value="' . $nonce . '" />';
		$r .= '<input type="hidden" name="ID" value="' . $post->ID . '" />';
		$r .= '<textarea cols="80" rows="25" name="wordpress-pastebin">';
		$r .= $post->post_content;
		$r .= '</textarea>';
		$r .= '<br /><input type="submit" value="' .
			__( 'Update paste', 'wordpress-pastebin' ) . '">';
		$r .= '</form>';
		return $content . $r;
	}

	/**
	 * Get the highlighter plugin that is used
	 *
	 * @return string highlighter plugin
	 * @todo make this configurable on a per-post basis
	 * @since 0.0.1
	 */
	function get_highlighter() {
		$hl = $this->get_option( 'highlighter' );
		if ( !$hl ) {
			$highlighters = $this->get_highlighter_list();
			$hl = $highlighters[0];
		}
		return $hl;
	}

	/**
	 * Add pastes to the query when requested
	 *
	 * @return none
	 * @since unknown
	 */
	function add_visibility_filters() {
		if ( $this->get_option( 'blog' ) && is_home() )
			add_filter( 'pre_get_posts', array( &$this, 'add_to_query' ) );
		if ( $this->get_option( 'post_tag' ) && is_tag() )
			add_filter( 'pre_get_posts', array( &$this, 'add_to_query' ) );
		if ( $this->get_option( 'cat' ) && is_category() )
			add_filter( 'pre_get_posts', array( &$this, 'add_to_query' ) );
	}

	/**
	 * Add Pastes to the main feed, see
	 * http://www.wpbeginner.com/wp-tutorials/how-to-add-custom-post-types-to-your-main-wordpress-rss-feed/
	 *
	 * @fixme don't remove other post types
	 * @since 0.0.1
	 *
	 * @return none
	 */
	function add_to_feed( $query_vars ) {
		if ( isset( $query_vars['feed'] ) && !isset( $query_vars['post_type'] ) )
			$query_vars['post_type'] = array(
				'post',
				$this->get_post_type_name()
			);
		return $query_vars;
	}

	/**
	 * Add the home link
	 *
	 * @param $content post content
	 * @return string post content
	 * @since unknown
	 */
	function thanks( $content ) {
		if ( get_post_type() != $this->get_post_type_name() || !is_single() )
			return $content;
		if ( $this->get_option( 'thanks_style' ) )
			$style = ' style="text-align: right; font-size: xx-small;" ';
		$content .= '<p class="wordpresspastebin"' . $style . '><a href="http://www.nkuttler.de/wordpress/wordpress-pastebin/">' . __( 'WordPress Pastebin plugin', 'wordpress-pastebin' ) . '</a>';
		return $content;
	}

	/**
	 * Add the note
	 *
	 * @param $content post content
	 * @return string post content
	 * @since unknown
	 */
	function add_note( $content ) {
		if ( get_post_type() != $this->get_post_type_name() )
			return $content;
		global $post;
		$custom = get_post_custom( $post->ID );
		$note = $custom['paste-note'][0];
		if ( !$note )
			return $content;
		$note = apply_filters( 'comment_text', $note );
		return '<div class="wordpresspastenote">' . $note . '</div>' . $content;
	}

	/**
	 * Add the paste post type to the query
	 *
	 * @param object $query Query
	 * @return Query
	 * @since 0.0.1
	 */
	function add_to_query( $query ) {
		$qv = $query->query_vars;
		if ( isset( $qv['suppress_filters'] ) && $qv['suppress_filters'] )
			return $query;
		$supported = $query->get( 'post_type' );
		if ( !$supported || $supported == 'post' )
			$supported = array( 'post', $this->get_post_type_name() );
		elseif ( is_array( $supported ) )
			array_push( $supported, $this->get_post_type_name() );
		$query->set( 'post_type', $supported );
		return $query;
	}

}
