<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Plugin Name: Smart Subscription
 * Description: Subscribe, Download file and Redirect after form submission
 * Plugin URI:  https://github.com/srits/DRForm
 * Version:     1.0
 * Author:      Aveza Tech
 * Author URI:  http://avezatech.com
 * Licence:     MIT
 * License URI: http://opensource.org/licenses/MIT
 */

add_action( 'admin_menu', array ( 'SS_DRF_Download_redirect_form', 'ss_drf_admin_menu' ) );
define("SS_DRF_PLUGIN_VERSION", "1.0" );
require_once("includes/functions.php");
require_once("includes/database_install.php");
require_once("includes/form_shortcode.php");
register_activation_hook(__FILE__,'ss_drf_plugin_data_tables_install');

function ss_drf_load_wp_media_files() {
  wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'ss_drf_load_wp_media_files' );	

global $wpdb;
class SS_DRF_Download_redirect_form
{
	/**
	 * Register the pages and the style and script loader callbacks.
	 *
	 * @wp-hook admin_menu
	 * @return  void
	 */
	public static function ss_drf_admin_menu()
	{		
		// built with get_plugin_page_hookname( $menu_slug, '' )
		$main = add_menu_page(
			'Forms',                         // page title
			'Forms',                         // menu title
			'edit_posts',                  // capability
			'ss_drforms',                         // menu slug
			array ( __CLASS__, 'ss_drf_render_form' ), // callback function
			'',
			50
		);
		
		// built with get_plugin_page_hookname( $menu_slug, $parent_slug)
		$sub1 = add_submenu_page(
			'ss_drforms',                         // parent slug
			'Add New',                     // page title
			'Add New',                     // menu title
			'edit_posts',                  // capability
			'ss_drform-new',                     // menu slug
			array ( __CLASS__, 'ss_drf_render_form_new' ) // callback function, same as above
		);
		$sub2 = add_submenu_page(
			'ss_drforms',                         // parent slug
			'Subscribers',                     // page title
			'Subscribers',                     // menu title
			'edit_posts',                  // capability
			'drlists',                     // menu slug
			array ( __CLASS__, 'ss_drf_render_form_lists' ) // callback function, same as above
		);	
		$sub3 = add_submenu_page(
			NULL,                         // parent slug
			'Edit Form',                     // page title
			'Edit Form',                     // menu title
			'edit_posts',                  // capability
			'ss_drform-edit',                     // menu slug
			array ( __CLASS__, 'ss_drf_render_form_edit' ) // callback function, same as above
		);	
		$sub4 = add_submenu_page(
			ss_drforms,                         // parent slug
			'Export',                     // page title
			'Export',                     // menu title
			'edit_posts',                  // capability
			'ss_drform-export',                     // menu slug
			array ( __CLASS__, 'ss_drf_render_form_export' ) // callback function, same as above
		);						
	 
                function ss_drf_rename_top_level_menus() {
                   ss_drf_rename_top_level_menu( 'Forms', 'Smart Subscription' );
                }
                 
                add_action( 'admin_menu',  'ss_drf_rename_top_level_menus' , 1000 );

                add_option("ss_drf_isdwnlfile", "", "", "yes");
                add_option("ss_drf_dwnlfile", "", "", "yes");
                add_option("ss_drf_isredirect", "", "", "yes");
                add_option("ss_drf_redirecturl", "", "", "yes");

                function ss_drf_admin_init() {
	                if ( function_exists('register_setting') ) {
		                register_setting('ss_drf_settings', 'option-1', '');
	                }
                }
                add_action('admin_init', 'ss_drf_admin_init'); 
                
                function ss_drf_rename_top_level_menu( $original, $new ) {
                   global $menu;
                 
                   foreach( $menu AS $k => $v ) {
                      if( $original == $v[0] ) {
                         $menu[$k][0] = $new;
                      }
                   }
                }
                
		/* See http://wordpress.stackexchange.com/a/49994/73 for the difference
		 * to "'admin_enqueue_scripts', $hook_suffix"
		 */
		foreach ( array ( $main,$sub1,$sub2,$sub3,$sub4 ) as $slug )
		{
			// make sure the style callback is used on our page only
			add_action(
				"admin_print_styles-$slug",
				array ( __CLASS__, 'ss_drf_enqueue_style' )
			);
			// make sure the script callback is used on our page only
			add_action(
				"admin_print_scripts-$slug",
				array ( __CLASS__, 'ss_drf_enqueue_script' )
			);
		}

	}

	public static function ss_drf_render_form()
	{	
		global $title;

		print '<div class="wrap sdrstyle">';
		print "<h2 style='position: relative;'>$title<a href='".get_option('siteurl')."/wp-admin/admin.php?page=ss_drform-new' class='add-new-h2 expt'>Add New</a>";
		
		if(isset($_GET['s']) && $_GET['s']!=''){
		print '<span class="subtitle">Search results for "'.sanitize_text_field($_GET['s']).'"</span>';		
		}
		print "</h2>";

		$file = plugin_dir_path( __FILE__ ) . "includes/ss_drf_render_form.php";

		if ( file_exists( $file ) )
			require $file;

		print '</div>';
	}

	public static function ss_drf_render_form_new()
	{
		global $title;

		print '<div class="wrap sdrstyle">';
		print "<h2>$title</h2>";

		$file = plugin_dir_path( __FILE__ ) . "includes/ss_drf_render_form_new.php";

		if ( file_exists( $file ) )
			require $file;

		print '</div>';
	}
	
	public static function ss_drf_render_form_lists()
	{
		global $title;

		print '<div class="wrap sdrstyle">';
		print '<h2>'.$title.'<a class="add-new-h2 expt bkp" href="'.get_option('siteurl').'/wp-admin/admin.php?page=ss_drforms"><span>Back</span></a></h2>';

		$file = plugin_dir_path( __FILE__ ) . "includes/ss_drf_render_form_lists.php";

		if ( file_exists( $file ) )
			require $file;

		print '</div>';
	}	

	public static function ss_drf_render_form_edit()
	{
		global $title;

		print '<div class="wrap sdrstyle">';		
		print "<h2>Edit Form</h2>";

		$file = plugin_dir_path( __FILE__ ) . "includes/ss_drf_render_form_edit.php";

		if ( file_exists( $file ) )
			require $file;

		print '</div>';
	}		

	public static function ss_drf_render_form_export()
	{
		global $title;

		print '<div class="wrap sdrstyle">';		
		print "<h2>$title</h2>";

		$file = plugin_dir_path( __FILE__ ) . "includes/ss_drf_render_form_export.php";

		if ( file_exists( $file ) )
			require $file;

		print '</div>';
	}		
	
	/**
	 * Load stylesheet on our admin page only.
	 *
	 * @return void
	 */
	public static function ss_drf_enqueue_style()
	{
		wp_register_style(
			'ss_drf_css',
			plugins_url( 'includes/ss_drf.css', __FILE__ )
		);
		wp_enqueue_style( 'ss_drf_css' );
	}

	/**
	 * Load JavaScript on our admin page only.
	 *
	 * @return void
	 */
	public static function ss_drf_enqueue_script()
	{	
		wp_register_script(
			'sel_js',
			plugins_url( 'includes/select.js', __FILE__ ),
			array(),
			FALSE,
			TRUE
		);		
		wp_register_script(
			'ss_drf_js',
			plugins_url( 'includes/ss_drf.js', __FILE__ ),
			array(),
			FALSE,
			TRUE
		);
		wp_enqueue_script( 'sel_js' );
		wp_enqueue_script( 'ss_drf_js' );
	}
	

	/**
	 * List available global variables.
	 *
	 * @return void
	 */
	protected static function ss_drf_list_globals()
	{
		print '<h2>Global variables</h2><table class="code">';

		ksort( $GLOBALS );
		foreach ( $GLOBALS as $key => $value )
		{
			print '<tr><td>$' . esc_html( $key ) . '</td><td>';

			if ( ! is_scalar( $value ) )
			{
				print '<var>' . gettype( $value ) . '</var>';
			}
			else
			{
				if ( FALSE === $value )
					$show = '<var>FALSE</var>';
				elseif ( '' === $value )
				$show = '<var>""</var>';
				else
					$show = esc_html( $value );

				print $show;
			}

			print '</td></tr>';
		}

		print '</table>';
	}

	/**
	 * Inspect program logic.
	 *
	 * @param array $backtrace
	 * @return void
	 */
	protected static function ss_drf_list_backtrace( $backtrace )
	{
		print '<h2>debug_backtrace()</h2><ol class="code">';

		foreach ( $backtrace as $item )
		{
			print '<li>';
			if ( isset ( $item['class'] ) )
				print $item['class'] . $item['type'];

			print $item['function'];

			if ( isset ( $item['args'] ) )
				print '<pre>args = ' . print_r( $item['args'], TRUE ) . '</pre>';

			if ( isset ( $item['file'] ) )
				print '<br>' . $item['file'] . ' line: ' . $item['line'];

			print "\n";
		}

		print '</ol>';
	}

}
