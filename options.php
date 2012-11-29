<?php
/**
 *  Admin options page. Creates a page to set your OAuth settings for the Yelp API v2.
 *  A special thanks to the Yelp It plugin for the great code!
 *
 */

register_activation_hook(__FILE__, 'yelp_widget_activate');
register_uninstall_hook(__FILE__, 'yelp_widget_uninstall');
add_action('admin_init', 'yelp_widget_init');
add_action('admin_menu', 'yelp_widget_add_options_page');

// Delete options when uninstalled
function yelp_widget_uninstall() {
    delete_option('yelp_widget_settings');
    delete_option('yelp_widget_consumer_key');
    delete_option('yelp_widget_consumer_secret');
    delete_option('yelp_widget_token');
    delete_option('yelp_widget_token_secret');
}

// Run function when plugin is activated
function yelp_widget_activate() {

    $options = get_option('yelp_widget_settings');

}

// Purely for debugging, do not uncomment this unless you want to delete all your settings
// yelp_widget_uninstall();

//Yelp Options Page
function yelp_widget_add_options_page() {
    // Add the menu option under Settings, shows up as "Yelp API Settings" (second param)
    $page = add_submenu_page('options-general.php',  //The parent page of this menu
                             'Yelp Widget Pro Settings',  //The Menu Title
                             'Yelp Widget Pro', //The Page Title
                             'manage_options',  // The capability required for access to this item
                             'yelp_widget',  // the slug to use for the page in the URL
                             'yelp_widget_options_form'); // The function to call to render the page

     /* Using registered $page handle to hook script load */
     add_action('admin_print_scripts-' . $page, 'yelp_options_scripts');


}
//Add Yelp Widget Pro option scripts to admin head - will only be loaded on plugin options page
function yelp_options_scripts() {
        //register our stylesheet
        wp_register_style('yelp_widget_css', WP_PLUGIN_URL . '/yelp-widget-pro/style/options.css');
        // It will be called only on plugin admin page, enqueue our stylesheet here
        wp_enqueue_style('yelp_widget_css');
}

//Load Widget JS Script ONLY on Widget page
function yelp_widget_scripts($hook){
       if($hook == 'widgets.php'){
           wp_enqueue_script('yelp_widget_admin_scripts', WP_PLUGIN_URL . '/yelp-widget-pro/js/admin-widget.js');
           wp_enqueue_style('yelp_widget_admin_css', WP_PLUGIN_URL . '/yelp-widget-pro/style/admin-widget.css');
       } else {
           return;
       }
}
add_action('admin_enqueue_scripts','yelp_widget_scripts');

//Initiate the Yelp Widget
function yelp_widget_init() {
    // Register the yelp_widget settings as a group
    register_setting('yelp_widget_settings', 'yelp_widget_settings');

    //call register settings function
	add_action( 'admin_init', 'yelp_widget_options_css' );

    add_action( 'admin_menu', 'my_plugin_admin_menu' );
    add_action( 'admin_init', 'yelp_widget_options_scripts' );


}

// Output the yelp_widget option setting value
function yelp_widget_option($setting, $options) {
    // If the old setting is set, output that
    if (get_option($setting) != '') {
        echo get_option($setting);
    } else if (is_array($options)) {
        echo $options[$setting];
    }
}

// Generate the admin form
function yelp_widget_options_form() { ?>

<div class="wrap" xmlns="http://www.w3.org/1999/html">
        <!-- Plugin Title -->
        <div id="icon-plugins" class="icon32"><br></div>
        <h2>Yelp Widget Pro Settings</h2>



        <div class="metabox-holder">

            <div class="postbox-container" style="width:75%">

                <form id="yelp-settings" method="post" action="options.php">

                    <div id="main-sortables" class="meta-box-sortables ui-sortable">
                        <div class="postbox" id="yelp-widget-intro">
                            <div class="handlediv" title="Click to toggle"><br></div>
                            <h3 class="hndle"><span>Yelp Widget Pro Introduction</span></h3>
                            <div class="inside">
                                  <p>Thanks for choosing Yelp Widget Pro! <strong>To start using Yelp Widget Pro you must have a valid Yelp API key</strong>.  Don't worry, it's <em>free</em> and very easy to get one!</p>

                                <p><strong>Yelp Widget Pro Activation Instructions:</strong></p>

                                <ol>
                                    <li>Sign into Yelp or create an account if you don't have one already</li>
                                    <li>Once logged in, <a href="http://www.yelp.com/developers/getting_started/api_access" target="_blank">sign up for API access</a></li>
                                    <li>After you have been granted an API key copy-and-paste the API v2.0 information into the appropriate fields below</li>
                                    <li>Click update to activate and begin using Yelp Widget Pro</li>
                                </ol>

                                <div class="adminFacebook">
                                   <p><strong>Like this plugin?  Give it a like on Facebook:</strong></p>
                                   <iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.facebook.com%2Fpages%2FWordImpressed%2F130943526946924&amp;layout=standard&amp;show_faces=false&amp;width=450&amp;action=like&amp;colorscheme=light&amp;height=28" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:28px;" allowTransparency="true"></iframe>
                                 </div>

                              </div><!-- /.inside -->
                        </div><!-- /#yelp-widget-intro -->

                        <div class="postbox" id="api-options">

                             <h3 class="hndle"><span>Yelp API v2.0 Information</span></h3>

                             <div class="inside">
                                <?php
                                    // Tells Wordpress that the options we registered are being
                                    // handled by this form
                                    settings_fields('yelp_widget_settings');

                                    // Retrieve stored options, if any
                                    $options = get_option('yelp_widget_settings');

                                    // Debug, show stored options
                                    // echo '<pre>'; print_r($options); echo '</pre>';
                                ?>

                                <div class="control-group">
                                    <div class="control-label">
                                        <label for="yelp_widget_consumer_key">
                                            Consumer Key:
                                        </label>
                                    </div>
                                    <div class="controls">
                                        <input type="text" id="yelp_widget_consumer_key"
                                            name="yelp_widget_settings[yelp_widget_consumer_key]"
                                            value="<?php yelp_widget_option('yelp_widget_consumer_key', $options); ?>"
                                        />
                                    </div>
                                </div>

                                <div class="control-group">
                                    <div class="control-label">
                                        <label for="yelp_widget_consumer_secret">
                                            Consumer Secret:
                                        </label>
                                    </div>
                                    <div class="controls">
                                        <input type="text" id="yelp_widget_consumer_secret"
                                            name="yelp_widget_settings[yelp_widget_consumer_secret]"
                                            value="<?php
                                                yelp_widget_option('yelp_widget_consumer_secret', $options);
                                            ?>"
                                        />
                                    </div>
                                </div>

                                <div class="control-group">
                                    <div class="control-label">
                                        <label for="yelp_widget_token">
                                            Token:
                                        </label>
                                    </div>
                                    <div class="controls">
                                        <input type="text" id="yelp_widget_token"
                                            name="yelp_widget_settings[yelp_widget_token]"
                                            value="<?php yelp_widget_option('yelp_widget_token', $options); ?>"
                                        />
                                    </div>
                                </div>

                                <div class="control-group">
                                    <div class="control-label">
                                        <label for="yelp_widget_token_secret">
                                            Token Secret:
                                        </label>
                                    </div>
                                    <div class="controls">
                                        <input type="text" id="yelp_widget_token_secret"
                                            name="yelp_widget_settings[yelp_widget_token_secret]"
                                            value="<?php
                                                yelp_widget_option('yelp_widget_token_secret', $options);
                                            ?>"
                                        />
                                    </div>
                                </div>
                                </div><!-- /.inside -->
                        </div><!-- /#api-settings -->

                        <div class="postbox" id="yelp-widget-options">

                            <h3 class="hndle"><span>Yelp Widget Pro Settings</span></h3>
                            <div class="inside">
                                <div class="control-group">
                                    <div class="control-label">
                                        <label for="yelp_widget_disable_css">Disable Plugin CSS Output:</label>
                                    </div>
                                    <div class="controls">
                                        <input type="checkbox" id="yelp_widget_disable_css"
                                            name="yelp_widget_settings[yelp_widget_disable_css]"
                                            value="1"
                                            <?php
                                                checked(
                                                    1,
                                                    $options['yelp_widget_disable_css']
                                                );
                                            ?>
                                        />
                                    </div>
                                </div>

                           </div><!-- /.inside -->
                        </div><!-- /#yelp-widget-options -->
                        <div class="control-group">
                           <div class="controls">
                               <input class="button-primary" type="submit" name="submit" value="<?php _e('Update'); ?>" />
                           </div>
                       </div>
                </form>
            </div><!-- /#main-sortables -->
        </div><!-- /.postbox-container -->
        <div class="alignright" style="width:24%">
            <div id="sidebar-sortables" class="meta-box-sortables ui-sortable">
                <div id="yelp-widget-pro-support" class="postbox">
                    <div class="handlediv" title="Click to toggle"><br></div><h3 class="hndle"><span>Need Support?</span></h3>
                    <div class="inside">
                    <p>If you have any problems with this plugin or ideas for improvements or enhancements, please use the <a href="http://wordpress.org/support/plugin/yelp-widget-pro" target="_blank">Support Forums</a>.</p>
                    </div><!-- /.inside -->
                </div><!-- /.yelp-widget-pro-support -->
                <div id="yelp-widget-coming-soon" class="postbox">
                    <div class="handlediv" title="Click to toggle"><br></div><h3 class="hndle"><span>Yelp Widget Pro News</span></h3>
                    <div class="inside">
                        <p>We're working hard to release new features soon to be available in the free and upcoming premium version of Yelp Widget Pro:</p>
                        <ol>
                            <li><strong>Yelp Review Snippets</strong> - Display up to 3-reviews excerpts per business.  Widget will display the user's avatar, star rating and 2-3 sentences of their excerpt.</li>
                            <li><strong>Shortcode</strong> - Display Yelp business ratings and reviews anywhere on your site using a configurable shortcode</li>
                            <li><strong>Image Size Variations</strong> - Choose the size to display your images</li>
                        </ol>
                    </div><!-- /.inside -->
                </div><!-- /.yelp-widget-coming-soon -->
            </div>

        </div>
    </div><!-- /.metabox-holder -->
</div><!-- /#wrap -->

<?php
} //end yelp_widget_options_form
?>