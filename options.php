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
//
//    // If the setting isn't already an array, it was never instantiated
//    if (!is_array($options)) {
//        $arr = array(
//            'yelp_widget_consumer_key'     => '',
//            'yelp_widget_consumer_secret'  => '',
//            'yelp_widget_token'            => '',
//            'yelp_widget_token_secret'     => '',
//            'yelp_widget_disable_css'  => false
//        );
//
//        update_option('yelp_widget_settings', $arr);
//    }
//
}




// Purely for debugging, do not uncomment this unless you want to delete all your settings
// yelp_widget_uninstall();

function yelp_widget_add_options_page() {
    // Add the menu option under Settings, shows up as "Yelp API Settings" (second param)
    add_submenu_page('options-general.php', 'Yelp API Settings', 'Yelp Widget Pro', 'manage_options', 'yelp_widget', 'yelp_widget_options_form');
    // Add the CSS for styling the options page
    yelp_widget_options_css();

}
//Add Yelp Widget Pro option styles to admin head
function yelp_widget_options_css() {
        //register our stylesheet
        wp_register_style('yelp_widget_css', WP_PLUGIN_URL . '/yelp-widget-pro/style/options.css');
        // It will be called only on plugin admin page, enqueue our stylesheet here
        wp_enqueue_style('yelp_widget_css');
}

//Initiate the Yelp Widget
function yelp_widget_init() {
    // Register the yelp_widget settings as a group
    register_setting('yelp_widget_settings', 'yelp_widget_settings');

    //call register settings function
	add_action( 'admin_init', 'yelp_widget_options_css' );

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

    <div class="wrap">
        <!-- Plugin Title -->
        <div id="icon-plugins" class="icon32"><br></div>
        <h2>Yelp Widget Pro Settings</h2>



        <div class="metabox-holder" style="padding:0;">
            <div class="postbox-container" style="width:75%">
                <form id="yelp-settings" method="post" action="options.php">

                <div id="main-sortables" class="meta-box-sortables ui-sortable">


                <p>Thanks for choosing Twitter Widget Pro!  To start using Yelp Widget Pro you must have a valid Yelp API key.  Don't worry, it's very easy to get one!  First, sign into Yelp and <a href="http://www.yelp.com/developers/getting_started/api_access" target="_blank">sign up for API access</a>. Once you have been granted an API key enter the API v2.0 information in the fields below and click update to begin using Twitter Widget Pro.</p>

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
            </div><!-- /#main-sortables -->
        </div><!-- /.postbox-container -->
        </form><!-- /options form -->
    </div><!-- /.metabox-holder -->
</div><!-- /#wrap -->

<?php
} //end yelp_widget_options_form
?>