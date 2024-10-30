<?php
/*
@wordpress-plugin
Plugin Name: BuddyPress Power SEO
Description:        This plugin enables SEO functionality for your BuddyPress-powered social network or community.
Plugin URI:        https://wordpress.org/plugins/bp-power-seo/
Version: 1.2
Author: sooskriszta
Author URI: https://profiles.wordpress.org/sooskriszta#content-plugins
Text Domain: bp-power-seo
*/

if ( ! defined( 'ABSPATH' ) ) exit;

add_action('admin_init', function() {
    if ( !is_plugin_active( 'buddypress/bp-loader.php' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );

        add_action( 'admin_notices', function() {
            ?>
            <div class="notice notice-error is-dismissible">
                <p><?php _e( '<a href="https://buddypress.org/" target="_blank">BuddyPress</a> plugin is required for "BP Power SEO" to work', 'SBSR_TEXT_DOMAIN' ); ?></p>
            </div>
            <?php
        });

        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    } 
});

require_once('lib/admin.php' );
require_once('lib/site.php' );
add_action( 'admin_enqueue_scripts', 'bpps_load_admin_style' );
function bpps_load_admin_style() {
	wp_enqueue_style( 'admin_bpp_css', plugin_dir_url( __FILE__ ) . 'assets/style.css', false, '1.0.0' );
	wp_enqueue_script('admin_bpp_js',plugin_dir_url( __FILE__ ) . 'assets/bpp.js',false,false,true);
}

$main_pages=get_option('bp-pages');

global $bpp_title_defaults;
$bpp_title_defaults['bpp_group_list']=get_the_title($main_pages['groups']);
$bpp_title_defaults['bpp_group_profile']='%%groupname%% %%sitename%%';
$bpp_title_defaults['bpp_member_list']=get_the_title($main_pages['members']);
$bpp_title_defaults['bpp_member_profile']='%%membername%% %%sitename%%';
$bpp_title_defaults['bpp_member_profile_tabs']='%%membername%% %%tabname%% %%sitename%%';
$bpp_title_defaults['bpp_custom_fields']='%%searchphrase%%  %%sitename%%';

$gl_markup_add=array('additionalName (Middle Name)','honorificPrefix','honorificSuffix','gender','birthDate','birthPlace','nationality','telephone','email','address','jobTitle','worksFor','workLocation','performerIn','parent','spouse','sibling','relatedTo','seeks','funder','sponsor','owns','affiliation','brand','award','weight','duns','globalLocationNumber','naics','deathDate','deathPlace');

global $sitemap_update_range;
$sitemap_update_range[900]='every 15 minutes';
$sitemap_update_range[1800]='every 30 minutes';
$sitemap_update_range[3600]='every 1 hour';
$sitemap_update_range[10800]='every 3 hours';
$sitemap_update_range[21600]='every 6 hours';
$sitemap_update_range[43200]='every 12 hours ';
$sitemap_update_range[86400]='every 1 day';
$sitemap_update_range[172800]='every 2 days';


function bpps_activate() {
	global $bpp_title_defaults;
	//var_dump($bpp_title_defaults);die();
    foreach($bpp_title_defaults as $k=>$v){
		update_option($k,$v);
	}

}
register_activation_hook( __FILE__, 'bpps_activate' );