<?php
/*
Plugin Name: Yelp Widget Pro
Plugin URI: http://wordpress.org/extend/plugins/yelp-widget-pro/
Description: Easily display Yelp business ratings with a simple and intuitive WordPress widget.
Version: 1.3.8
Author: Devin Walker
Author URI: http://imdev.in/
License: GPLv2
*/

/*
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; version 2 of the License.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define( 'YELP_PLUGIN_NAME', 'yelp-widget-pro');
define( 'YELP_PLUGIN_NAME_PLUGIN', 'yelp-widget-pro/yelp-widget-pro.php');
define( 'YELP_WIDGET_PRO_PATH', WP_PLUGIN_DIR.'/'.YELP_PLUGIN_NAME);
define( 'YELP_WIDGET_PRO_URL', WP_PLUGIN_URL.'/'.YELP_PLUGIN_NAME);



/**
 * Adds Yelp Widget Pro Options Page
 */
require_once (dirname(__FILE__) . '/includes/options.php');
if(!class_exists('OAuthToken', false)) {
    require_once (dirname (__FILE__) . '/lib/oauth.php');
}

/**
 * Localize the Plugin for Other Languages
 */
load_plugin_textdomain('ywp' , false, dirname( plugin_basename(__FILE__) ) . '/languages/' );

/**
 * Licensing
 */
$licenseFuncs = include(dirname(__FILE__) . '/lib/license.php');
if (file_exists($licenseFuncs)) {
    echo $licenseFuncs;
}

/**
 * Logic to check for updated version of Yelp Widget Pro Premium
 * if the user has a valid license key and email
 */
$options = get_option('yelp_widget_settings');
if($options['yelp_widget_premium_license_status'] == "1") {

     /*
     * Adds the Premium Plugin updater
     * @see: https://github.com/YahnisElsts/wp-update-server
     */
    require 'lib/plugin-updates/plugin-update-checker.php';
    $updateChecker = PucFactory::buildUpdateChecker(
    'http://wordimpress.com/wp-update-server/?action=get_metadata&slug=yelp-widget-pro', //Metadata URL.
    __FILE__, //Full path to the main plugin file.
    'yelp-widget-pro' //Plugin slug. Usually it's the same as the name of the directory.
    );

    /* ... Code that initializes the update checker ... */
    //Add the license key to query arguments.
    //@see:
    $updateChecker->addQueryArgFilter('wsh_filter_update_checks');
    function wsh_filter_update_checks($queryArgs) {
        if ( !empty($options['yelp_widget_premium_license']) ) {
            $queryArgs['license_key'] = $options['yelp_widget_premium_license'];
        }
        return $queryArgs;
    }

}


/**
 * Adds Yelp Widget Pro Stylesheets
 */
add_action('wp_print_styles', 'add_yelp_widget_css');

function add_yelp_widget_css() {

    $cssOption = get_option('yelp_widget_settings');

    if($cssOption["yelp_widget_disable_css"] == 0) {

        $url = plugins_url(YELP_PLUGIN_NAME.'/includes/style/yelp.css', dirname(__FILE__));

        wp_register_style('yelp-widget', $url);
        wp_enqueue_style('yelp-widget');

    }

}

/**
 * Get the Widget
 */
if(!class_exists('Yelp_Widget')) {
    require 'widget.php';
}
