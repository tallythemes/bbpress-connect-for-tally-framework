<?php
/**
 * Plugin Name: bbPress Connect For Tally Framework
 * Plugin URI:  http://tallythemes.com/
 * Description: Add basic bbPress templating and Style for  <strong> Tally Framework</strong>
 * Author:      TallyThemes
 * Author URI:  http://tallythemes.com/
 * Version:     0.1
 * Text Domain: bbpresstallyc_textdomain
 * Domain Path: /languages/
 * Name Space: bbpresstallyc
 * Name Space: BBPRESSTALLYC
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

$path_dir = trailingslashit(str_replace('\\','/',dirname(__FILE__)));
$path_abs = trailingslashit(str_replace('\\','/',ABSPATH));

define('BBPRESSTALLYC', 'bbPress Connect For Tally Framework' );
define('BBPRESSTALLYC_URL', site_url(str_replace( $path_abs, '', $path_dir )) );
define('BBPRESSTALLYC_DRI', $path_dir );
define('BBPRESSTALLYC_TEMPLATE', BBPRESSTALLYC_DRI.'bbpress' );
define('BBPRESSTALLYC_VERSION', 0.1);


class bbpresstallyc{
	public $custome_css;
	
	function __construct(){
		add_action('init', array($this,'load_textdomain'));
		add_action('after_setup_theme', array($this,'after_setup_theme'));
		
		$this->custome_css = apply_filters('bbpresstallyc_custom_css', true);
	}
	
	
	
	/** Load TextDomain ********************************************************************/
	/**
	 * Add languages files.
	 *
	 * @since 0.1
	 *
	 * @uses load_plugin_textdomain()
	 */
	function load_textdomain(){
		load_plugin_textdomain( 'bbpresstallyc_textdomain', false, dirname(plugin_basename(__FILE__)).'/languages/' );
	}
	
	
	
	/** after_setup_theme hook function ****************************************************/
	/**
	 * This function contain all elements that's need 
	 * to attached in "after_setup_theme" hook.
	 *
	 * @since 0.1
	 *
	 * @used with "after_setup_theme" hook
	 */
	function after_setup_theme(){
		/** Fail silently if WooCommerce is not activated */
		if ( ! in_array( 'bbpress/bbpress.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) return;	
		if(!function_exists('tally_option')) return;	
	
		/* Setup bbPress sidebar*/
		register_sidebar( array(
			'name'			=> __('bbPress Sidebar', 'bbpresstallyc_textdomain'),
			'id'			=> 'tally_bbpress',
			'description'	=> __('bbPress shop Sidebar Widgets', 'bbpresstallyc_textdomain'),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'	=> "</div><div class='clear' style='height:30px;'></div>",
			'before_title'	=> '<h4 class="heading">',
			'after_title'	=> '</h4>',
		));
		add_action( 'tally_sidebar', array($this,'add_sidebar') );
		add_filter('tally_sidebar_active', array($this,'disable_theme_sidebar'));
		
		add_action('tally_reset_loops', array($this,'add_page_content'));
		
		add_filter ('bbp_no_breadcrumb', '__return_true');
		
		add_action('wp_enqueue_scripts', array($this,'custom_scripts'));
	}
	
	
	
	/** Add Sidebar To the theme ****************************************************/
	/**
	 * This function add "tally_bbpress" in the theme.
	 *
	 * @since 0.1
	 *
	 * @used with "tally_sidebar" hook
	 */
	function add_sidebar(){
		if(is_bbpress()){
			if ( ! dynamic_sidebar( 'tally_bbpress' ) && current_user_can( 'edit_theme_options' )  ) {
				if(function_exists('tally_default_widget_area_content')){ tally_default_widget_area_content( __( 'bbPress Sidebar Widget Area', 'tally_textdomain' ) ); };
			}	
		}
	}
	
	
	/** Disable Theme Sidebar *****************************************************/
	/**
	 * This function disable deafult sidebar of the theme
	 *
	 * @since 0.1
	 *
	 * @used with "tally_sidebar_active" filter
	 */
	function disable_theme_sidebar($active){
		if(is_bbpress()){
			$active = false;
		}
		
		return $active;
	}
	
	
	
	/** Make the page content *****************************************************/
	/**
	 * This function remove some unwanted post element from the theme
	 *
	 * @since 0.1
	 *
	 * @used with "tally_reset_loops" hook
	 */
	function add_page_content($active){
		if(is_bbpress()){
			tally_defaults_reset_loops();
		
			remove_action( 'tally_entry_header', 'tally_do_post_media', 4 );
			remove_action( 'tally_entry_header', 'tally_entry_header_markup_open', 5 );
			remove_action( 'tally_entry_header', 'tally_entry_header_markup_close', 15 );
			remove_action( 'tally_entry_header', 'tally_do_post_title' );
			remove_action( 'tally_entry_header', 'tally_do_post_info', 12 );
			remove_action( 'tally_entry_header', 'tally_do_post_format_link', 13 );
			remove_action( 'tally_entry_content', 'tally_do_post_format_quote', 10 );
			remove_action( 'tally_entry_content', 'tally_do_post_content_nav', 12 );
			remove_action( 'tally_entry_footer', 'tally_entry_footer_markup_open', 5 );
			remove_action( 'tally_entry_footer', 'tally_entry_footer_markup_close', 15 );
			remove_action( 'tally_entry_footer', 'tally_do_post_meta' );
			remove_action( 'tally_after_entry', 'tally_do_author_box_single', 8 );
			remove_action( 'tally_after_endwhile', 'tally_do_posts_nav' );
		}
		
		return $active;
	}
	
	
	
	/** Load Custom frontend scripts ***********************************************/
	/**
	 * This function add custom css and jsvascript for bbpress
	 *
	 * @since 0.1
	 *
	 * @used with "bbp_enqueue_scripts" hook
	 */
	function custom_scripts(){
		if(is_bbpress()){
			if($this->custome_css == true){
				wp_deregister_style( 'bbp-default' );
				wp_enqueue_style( 'bbp-default', BBPRESSTALLYC_URL.'assets/css/bbpress.css' );
			}
		}
	}
	
	
}// END of the class


new bbpresstallyc;