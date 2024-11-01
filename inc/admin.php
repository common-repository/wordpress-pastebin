<?php
/**
 * Review custom post type, admin section
 *
 * @package wordpress-pastebin
 * @subpackage admin
 * @since 0.0.1
 */
class WordPressPastebinAdmin extends WordPressPastebin {

	/**
	 * Version of the options format
	 *
	 * @since 0.0.1
	 * @var string
	 */
	var $version = '0.4';

	/**
	 * Our meta inputs
	 *
	 * @since 0.0.1
	 * @var array
	 */
	var $meta_fields = array(
		'paste-highlight',
		'paste-note'
	);

	/**
	 * Path to the main plugin file
	 *
	 * @since 0.0.1
	 * @var string
	 */
	var $plugin_file;

	/**
	 * PHP4-style constructor
	 *
	 * @since 0.0.1
	 */
	function WordPressPastebinAdmin() {
		$this->__construct();
	}

	/**
	 * Set up backend
	 *
	 * @since 0.0.1
	 */
	function __construct() {
		WordPressPastebin::__construct();
		// Activation hook
		register_activation_hook( $this->plugin_file, array( &$this, 'init' ) );
		// Add our edit post filter
		add_filter(
			'wp_insert_post_data',
			array( &$this, 'update_post_content' ),
			99
		);
		load_plugin_textdomain( 'wordpress-pastebin', false, 'wordpress-pastebin/translations' );
		add_action( 'admin_init', array ( &$this, 'register_settings' ) );
		add_action( 'admin_init', array ( &$this, 'add_meta_boxes' ) );
		add_action( 'admin_menu', array ( &$this, 'add_page' ) ) ;
		add_action( 'wp_insert_post', array( &$this, 'update_post_meta' ), 10, 2 );
		add_action( 'init', array( &$this, 'flush_rewrite_rules' ) );
		add_action( 'wp_insert_post', array( &$this, 'update_post_terms' ) );
	}

	/**
	 * Flush the rewrite rules
	 *
	 * @since unknown
	 */
	function flush_rewrite_rules() {
		if ( get_option( 'wordpress-pastebin-flush' ) ) {
			flush_rewrite_rules();
			delete_option( 'wordpress-pastebin-flush' );
		}
	}

	/**
	 * Initialize the plugin
	 *
	 * @since 0.0.1
	 */
	function init() {
		if ( !get_option ( 'wordpress-pastebin' ) )
			add_option ( 'wordpress-pastebin', $this->defaults() );
		add_option( 'wordpress-pastebin-flush', true );
	}

	/**
	 * Return plugin default config
	 *
	 * @since 0.0.1
	 * @return array
	 */
	function defaults () {
		$defaults = array (
			'version'			=> $this->version,
			'shorten'			=> true,
			'default'			=> '',
			'feed'				=> false,
			'blog'				=> false,
			'tag'				=> true,
			'title'				=> true,
			'add_note'			=> false,
			'comments'			=> false,
			'thanks'			=> true,
			'thanks_style'		=> true,
			'autotag'			=> true,
			'cat'				=> false,
			'post_tag'			=> true,
			'autocat'			=> '',
			'highlighter'		=> '',
			'frontend-editing'	=> false
		);
		return $defaults;
	}

	/**
	 * Return supported languages, must be supported by highlighter!
	 *
	 * @since 0.0.1
	 * @return array
	 */
	function languages() {
		return array(
			'',
			'abap',
			'actionscript',
			'actionscript3',
			'ada',
			'apache',
			'applescript',
			'apt_sources',
			'asm',
			'asp',
			'autoit',
			'avisynth',
			'bash',
			'bf',
			'bibtex',
			'blitzbasic',
			'bnf',
			'boo',
			'c',
			'c_mac',
			'caddcl',
			'cadlisp',
			'cil',
			'cfdg',
			'cfm',
			'cmake',
			'cobol',
			'cpp-qt',
			'cpp',
			'csharp',
			'css',
			'd',
			'dcs',
			'delphi',
			'diff',
			'div',
			'dos',
			'dot',
			'eiffel',
			'email',
			'erlang',
			'fo',
			'fortran',
			'freebasic',
			'genero',
			'gettext',
			'glsl',
			'gml',
			'bnuplot',
			'groovy',
			'haskell',
			'hq9plus',
			'html4strict',
			'idl',
			'ini',
			'inno',
			'intercal',
			'io',
			'java',
			'java5',
			'javascript',
			'kixtart',
			'klonec',
			'klonecpp',
			'latex',
			'lisp',
			'locobasic',
			'lolcode lotusformulas',
			'lotusscript',
			'lscript',
			'lsl2',
			'lua',
			'm68k',
			'make',
			'matlab',
			'mirc',
			'modula3',
			'mpasm',
			'mxml',
			'mysql',
			'nsis',
			'oberon2',
			'objc',
			'ocaml-brief',
			'ocaml',
			'oobas',
			'oracle11',
			'oracle8',
			'pascal',
			'per',
			'pic16',
			'pixelbender',
			'perl',
			'php-brief',
			'php',
			'plsql',
			'povray',
			'powershell',
			'progress',
			'prolog',
			'properties',
			'providex',
			'python',
			'qbasic',
			'rails',
			'rebol',
			'reg',
			'robots',
			'ruby',
			'sas',
			'scala',
			'scheme',
			'scilab',
			'sdlbasic',
			'smalltalk',
			'smarty',
			'sql',
			'tcl',
			'teraterm',
			'text',
			'thinbasic',
			'tsql',
			'typoscript',
			'vb',
			'vbnet',
			'verilog',
			'vhdl',
			'vim',
			'visualfoxpro',
			'visualprolog',
			'whitespace',
			'whois',
			'winbatch',
			'xml',
			'xorg_conf',
			'xpp',
			'z80'
		);
	}

	/**
	 * Reset the plugin config
	 *
	 * @return none
	 * @since 0.0.1
	 */
	 function restore_defaults() {
	 	$this->options = $this->defaults();
		update_option( 'wordpress-pastebin', $this->options );
	 }

	/**
	 * Load admin CSS style
	 *
	 * @since 0.0.1
	 */
	function css() {
		wp_register_style(
			'wordpress-pastebin',
			plugins_url( basename( $this->plugin_dir ) . '/css/admin.css' ),
			null,
			'0.0.1'
		);
		wp_enqueue_style( 'wordpress-pastebin' );
	}

	/**
	 * Add the options page
	 *
	 * @return none
	 * @since 0.0.1
	 */
	function add_page() {
		if ( current_user_can ( 'manage_options' ) && function_exists ( 'add_options_page' ) ) {
			$options_page = add_options_page ( __( 'WordPress Pastebin', 'wordpress-pastebin' ), __( 'WordPress Pastebin', 'wordpress-pastebin' ), 'manage_options', 'wordpress-pastebin', array ( &$this, 'admin_page' ) );
			add_action( 'admin_print_styles-' . $options_page, array( &$this, 'css' ) );
		}
	}

	/**
	 * Return a short post name
	 *
	 * @return string Our short permalink
	 * @since 0.0.1
	 */
	function short_name( $post_name ) {
		global $wpdb, $post;
		if ( !is_object( $post ) )
			return $post_name;
		$num	= (int) $post->ID;
		$slug	= base_convert( $num, 10, 36 );
		return $slug;
	}

	/**
	 * Update the post content before saving it. Add meta boxes and short slug
	 *
	 * @param object $post
	 * @return object
	 * @since 0.0.1
	 */
	function update_post_content( $data, $postarr = null ) {
		if ( $data['post_type'] == $this->get_post_type_name() ) {
			// preserve on bulk edit
			$content = @$_POST['paste-code'];
			if ( isset( $content ) )
				$data['post_content'] = $content;

			if ( $this->get_option( 'shorten' ) )
				$data['post_name'] = $this->short_name( $data['post_name'] );
		}
		return $data;
	}

	/**
	 * Auto-tag and categorize posts on save
	 *
	 * @param int $post_id The post ID
	 * @since 0.0.4
	 */
	function update_post_terms( $post_id ) {
		if ( $parent = wp_is_post_revision( $post_id ) )
			$post_id = $parent;
		$post = get_post( $post_id );
		if ( $post->post_type != $this->get_post_type_name() )
			return;
		if ( $this->get_option( 'autotag' ) && $this->get_option( 'post_tag' ) ) {
			$custom		= get_post_custom( $post_id );
			if ( isset( $custom['paste-highlight'] ) ) {
				$highlight	= $custom['paste-highlight'];
				wp_set_post_terms( $post_id, $highlight, 'post_tag', true );
			}
		}
		if ( $name = $this->get_option( 'autocat' ) ) {
			$categories	= wp_get_post_categories( $post_id );
			$autocat	= get_term_by( 'name', $name, 'category' );
			array_push( $categories, $autocat->term_id );
			wp_set_post_categories( $post_id, $categories );
		}
	}

	/**
	 * Update the paste metadata when a paste is inserted or updated
	 *
	 * @param integer $post_id post ID
	 * @param object $post post object
	 * @return none
	 * @since 0.0.1
	 */
	function update_post_meta( $post_id, $post = null ) {
		if ( $post->post_type == $this->get_post_type_name() ) {
			foreach ( $this->meta_fields as $key ) {
				// preserve on bulk edit
				if ( !isset( $_POST[$key] ) )
					continue;
				$value = @$_POST[$key];
				if ( empty( $value ) ) {
					delete_post_meta( $post_id, $key );
					continue;
				}
				if ( !update_post_meta( $post_id, $key, $value ) ) {
					add_post_meta( $post_id, $key, $value );
				}
			}
		}
	}

	/**
	 * Add our meta boxes
	 *
	 * @return none
	 * @since 0.0.1
	 */
	function add_meta_boxes() {
		add_meta_box(
			'wordpress-pastebin-language',
			__( 'Pick language', 'wordpress-pastebin' ),
			array( &$this, 'meta_box_highlight' ),
			$this->get_post_type_name(),
			'side',
			'default'
		);
		add_meta_box(
			'wordpress-pastebin-code',
			__( 'Paste your code', 'wordpress-pastebin' ),
			array( &$this, 'meta_box_code' ),
			$this->get_post_type_name(),
			'normal',
			'high'
		);
		if ( $this->get_option( 'add_note' ) )
			add_meta_box(
				'wordpress-pastebin-note',
				__( 'Add a note', 'wordpress-pastebin' ),
				array( &$this, 'meta_box_note' ),
				$this->get_post_type_name(),
				'normal',
				'high'
			);
	}

	/**
	 * The pastebin textarea
	 *
	 * @return none
	 * @since 0.0.1
	 */
	function meta_box_code() {
		global $post;
		$content = $post->post_content;
		echo '<textarea name="paste-code" rows="25" cols="80">' . $content . '</textarea>';
	}

	/**
	 * The note textarea
	 *
	 * @return none
	 * @since 0.0.1
	 */
	function meta_box_note() {
		global $post;
		$custom = get_post_custom( $post->ID );
		@$note = $custom['paste-note'][0];
		echo '<textarea name="paste-note" rows="5" cols="40">' . $note . '</textarea>';
	}

	/**
	 * Pick the correct highlighter plugin
	 *
	 * @return none
	 * @todo select box, check which plugin we'll use for languages
	 * @since 0.0.1
	 */
	function meta_box_highlight() {
		global $post;
		$custom = get_post_custom( $post->ID );
		if ( isset( $custom['paste-highlight'] ) )
			$highlight = $custom['paste-highlight'][0];
		if ( !isset( $highlight ) || $highlight == '' )
			$highlight = $this->get_option( 'default' );
		?>
		<table>
			<tr>
				<td>
					<label><?php _e( 'Highlight:', 'wordpress-pastebin' ); ?></label>
				</td>
				<td>
					<select name="paste-highlight"> <?php
						foreach( $this->languages() as $lang ) {
							if ( $lang == $highlight )
								$select = ' selected="selected" ';
							else
								$select = '';
							echo "<option $select>$lang</option>";
						} ?>
					</select>
				</td>
			</tr>
		</table> <?php
	}

	/**
	 * Whitelist the options
	 *
	 * @return none
	 * @since 0.0.1
	 */
	function register_settings () {
		register_setting( 'wordpress-pastebin', 'wordpress-pastebin' );
	}

	/**
	 * Checkbox helper
	 *
	 * @return none
	 * @since 0.0.1
	 */
	function checkbox( $label, $name, $comment = false ) { ?>
		<tr valign="top">
			<th scope="row"> <?php
				echo $label; ?>
			</th>
			<td> <?php
				$checked = '';
				if ( $this->get_option( $name ) )
					$checked = " checked='checked' ";
				echo '<input ' . $checked . 'name="wordpress-pastebin[' . $name . ']" type="checkbox"/>';
				if ( $comment )
					echo ' ' . $comment;
				?>
			</td>
		</tr> <?php
	}

	/**
	 * Select helper
	 *
	 * @param string $name Option name
	 * @param array $choices Possible choices (strings)
	 * @return none
	 * @since 0.0.5
	 */
	function select( $label, $name, $choices, $comment = false ) {
		$current = $this->get_option( $name ); ?>
		<tr valign="top">
			<th scope="row"> <?php
				echo $label; ?>
			</th>
			<td>
				<select name="wordpress-pastebin[<?php echo $name ?>]"> <?php
					foreach( $choices as $choice ) {
						if ( $choice == $current )
							$select = ' selected="selected" ';
						else
							$select = '';
						echo "<option $select>$choice</option>";
					} ?>
				</select> <?php
				if ( $comment )
					echo ' ' . $comment;
				?>
			</td>
		</tr> <?php
	}

	/**
	 * Output the options page
	 *
	 * @return none
	 * @since 0.0.1
	 */
	function admin_page() { ?>
		<div id="nkuttler" class="wrap" >
			<div id="nkcontent">
				<h2><?php _e( 'WordPress Pastebin', 'wordpress-pastebin' ) ?></h2>
				<p> <?php
					_e( 'With this plugin you can turn your blog into a pastebin.', 'wordpress-pastebin' ); ?>
				</p>
				<h3><?php _e( 'General Settings', 'wordpress-pastebin' ) ?></h3>
				<form action="options.php" method="post">
					<table class="form-table form-table-clearnone" > <?php
						settings_fields( 'wordpress-pastebin' );
						$this->checkbox(
							__( 'Use short permalinks', 'wordpress-pastebin' ),
							'shorten',
							__( "The alternative is to build the URI from the paste's title like for normal posts.", 'wordpress-pastebin' )
						);
						$this->select(
							__( 'Default language', 'wordpress-pastebin' ),
							'default',
							$this->languages(),
							__( 'Pick a default programming language.', 'wordpress-pastebin' )
						);
						$this->select(
							__( 'Default highlighter', 'wordpress-pastebin' ),
							'highlighter',
							$this->get_highlighter_list(),
							__( 'Pick a default syntax highlighting plugin.', 'wordpress-pastebin' )
						); ?>
					</table>
					<h3><?php _e( 'Paste features', 'wordpress-pastebin' ) ?></h3>
					<table class="form-table form-table-clearnone" > <?php
						$this->checkbox(
							__( 'Notes', 'wordpress-pastebin' ),
							'add_note',
							__( 'Notes allow you to add a little content above the paste.', 'wordpress-pastebin' )
						);
						$this->checkbox(
							__( 'Titles', 'wordpress-pastebin' ),
							'title',
							sprintf( __( "You want to build a <a href=\"%s\">custom template</a> for the paste custom post type if you disable titles. If you show the pastes anywhere on your site or in your feed you'll have to fix the display of that well.", 'wordpress-pastebin' ), 'http://codex.wordpress.org/Template_Hierarchy#Single_Post_display' )
						);
						$this->checkbox(
							__( 'Comments', 'wordpress-pastebin' ),
							'comments',
							__( 'Enable comments for the pastes.', 'wordpress-pastebin' )
						);
						$this->checkbox(
							__( 'Auto-Tag', 'wordpress-pastebin' ),
							'autotag',
							__( 'Automatically tag new pastes with the highlight language.', 'wordpress-pastebin' )
						);
						$this->checkbox(
							__( 'Categories', 'wordpress-pastebin' ),
							'cat',
							__( 'Enable categories for the pastes.', 'wordpress-pastebin' )
						);
						$this->checkbox(
							__( 'Tags', 'wordpress-pastebin' ),
							'post_tag',
							__( 'Enable tags for the pastes.', 'wordpress-pastebin' )
						);
						$cats = get_categories( 'hide_empty=0' );
						$categories = array( '' );
						foreach ( $cats as $cat ) {
							array_push( $categories, $cat->name );
						}
						$this->select(
							__( 'Auto-category', 'wordpress-pastebin' ),
							'autocat',
							$categories,
							__( 'Pick a post category to put your new pastes into automatically', 'wordpress-pastebin' )
						);
						$this->checkbox(
							__( 'Frontend editing', 'wordpress-pastebin' ),
							'frontend_editing',
							__( 'Show the edit form on the frontend', 'wordpress-pastebin' )
						);
						$this->checkbox(
							__( 'Anonymous pasting', 'wordpress-pastebin' ),
							'anonymous_posting',
							__( 'Allow anybody to publish pastes, use the shortcode <code>[wordpress-pastebin]</code> to show the form.', 'wordpress-pastebin' )
						); ?>
					</table>
					<h3><?php _e( 'Visibility settings', 'wordpress-pastebin' ) ?></h3>
					<table class="form-table form-table-clearnone" > <?php
						$this->checkbox(
							__( 'Show pastes in your main feed', 'wordpress-pastebin' ),
							'feed'
						);
						$this->checkbox(
							__( 'Show pastes on main blog page', 'wordpress-pastebin' ),
							'blog'
						);
						$this->checkbox(
							__( 'Thank the plugin author', 'wordpress-pastebin' ),
							'thanks',
							__( "Please support this plugin by allowing a link to it's home page below your pastes.", 'wordpress-pastebin' )
						);
						$this->checkbox(
							__( 'Style the thanks link', 'wordpress-pastebin' ),
							'thanks_style'
						); ?>
					</table>
					<p class="submit">
						<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" class="button-primary" />
					</p>
				</form>
			</div> <?php
			require_once( 'nkuttler.php' );
			nkuttler0_2_3_links( 'wordpress-pastebin' ); ?>
		</div> <?php
	}

}
