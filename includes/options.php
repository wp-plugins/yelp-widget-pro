<?php
/**
 *  Admin options page. Creates a page to set your OAuth settings for the Yelp API v2.
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

//Yelp Options Page
function yelp_widget_add_options_page() {
    // Add the menu option under Settings, shows up as "Yelp API Settings" (second param)
    $page = add_submenu_page('options-general.php',  //The parent page of this menu
                             __('Yelp Widget Pro Settings','ywp'),  //The Menu Title
                             __('Yelp Widget Pro','ywp'), //The Page Title
                             'manage_options',  // The capability required for access to this item
                             'yelp_widget',  // the slug to use for the page in the URL
                             'yelp_widget_options_form'); // The function to call to render the page

     /* Using registered $page handle to hook script load */
     add_action('admin_print_scripts-' . $page, 'yelp_options_scripts');


}
//Add Yelp Widget Pro option scripts to admin head - will only be loaded on plugin options page
function yelp_options_scripts() {

        //register admin JS
    wp_enqueue_script('yelp_widget_options_js', plugins_url( 'includes/js/options.js' , dirname(__FILE__) ));

        //register our stylesheet
        wp_register_style('yelp_widget_options_css', plugins_url( 'includes/style/options.css' , dirname(__FILE__) ));
        // It will be called only on plugin admin page, enqueue our stylesheet here
        wp_enqueue_style('yelp_widget_options_css');
}

//Load Widget JS Script ONLY on Widget page
function yelp_widget_scripts($hook){
       if($hook == 'widgets.php'){
           wp_enqueue_script('yelp_widget_admin_scripts', plugins_url( 'includes/js/admin-widget.js' , dirname(__FILE__) ));
           wp_enqueue_style('yelp_widget_admin_css', plugins_url( 'includes/style/admin-widget.css' , dirname(__FILE__) ));
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
    $value = "";
    // If the old setting is set, output that
    if (get_option($setting) != '') {
        $value = get_option($setting);
    } elseif (is_array($options)) {
        $value = $options[$setting];
    }

    return $value;

}



// Generate the admin form
function yelp_widget_options_form() { ?>

<div class="wrap" xmlns="http://www.w3.org/1999/html">

        <!-- Plugin Title -->
        <div id="icon-yelp" class=""></div>
        <h2><?php _e('Yelp Widget Pro Settings', 'ywp'); ?> </h2>
        <form id="yelp-settings" method="post" action="options.php">

        <div class="metabox-holder">

            <div class="postbox-container" style="width:75%">


                    <div id="main-sortables" class="meta-box-sortables ui-sortable">
                        <div class="postbox" id="yelp-widget-intro">
                            <div class="handlediv" title="Click to toggle"><br></div>
                            <h3 class="hndle"><span><?php _e('Yelp Widget Pro Introductions', 'ywp'); ?></span></h3>
                            <div class="inside">
                                  <p><?php _e('Thanks for choosing Yelp Widget Pro! <strong>To start using Yelp Widget Pro you must have a valid Yelp API key</strong>.  Don\'t worry, it\'s <em>free</em> and very easy to get one!','ywp'); ?></p>

                                <p><strong><?php _e('Yelp Widget Pro Activation Instructions:', 'ywp'); ?></strong></p>

                                <ol>
                                    <li><?php _e('Sign into Yelp or create an account if you don\'t have one already','ywp'); ?></li>
                                    <li><?php _e('Once logged in, <a href="http://www.yelp.com/developers/getting_started/api_access" target="_blank">sign up for API access','ywp'); ?></a></li>
                                    <li><?php _e('After you have been granted an API key copy-and-paste the API v2.0 information into the appropriate fields below','ywp'); ?></li>
                                    <li><?php _e('Click update to activate and begin using Yelp Widget Pro','ywp'); ?></li>
                                </ol>

                                <div class="adminFacebook">
                                   <p><strong><?php _e('Like this plugin?  Give it a like on Facebook:', 'ywp'); ?></strong></p>
                                    <iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2Fpages%2FWordImpress%2F353658958080509&amp;send=false&amp;layout=button_count&amp;width=100&amp;show_faces=false&amp;font&amp;colorscheme=light&amp;action=like&amp;height=21&amp;appId=220596284639969" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:21px;" allowTransparency="true"></iframe>
                                </div>

                              </div><!-- /.inside -->
                        </div><!-- /#yelp-widget-intro -->

                        <div class="postbox" id="api-options">

                             <h3 class="hndle"><span><?php _e('Yelp API v2.0 Information','ywp'); ?></span></h3>

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
                                            value="<?php echo yelp_widget_option('yelp_widget_consumer_key', $options); ?>"
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
                                            value="<?php echo yelp_widget_option('yelp_widget_consumer_secret', $options);  ?>" />
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
                                            value="<?php echo yelp_widget_option('yelp_widget_token', $options); ?>"  />
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
                                            value="<?php echo yelp_widget_option('yelp_widget_token_secret', $options); ?>" />
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
                                </div><!--/.control-group -->

                           </div><!-- /.inside -->
                        </div><!-- /#yelp-widget-options -->

                        <div class="control-group">
                           <div class="controls">
                               <input class="button-primary" type="submit" name="submit-button" value="<?php _e('Update'); ?>" />
                           </div>
                       </div>
            </div><!-- /#main-sortables -->
        </div><!-- /.postbox-container -->
        <div class="alignright" style="width:24%">
            <div id="sidebar-sortables" class="meta-box-sortables ui-sortable">
                <?php
                $licenseMetabox = include(YELP_WIDGET_PRO_PATH . '/lib/license-metabox.php');
                if(file_exists($licenseMetabox)){
                    echo $licenseMetabox;
                } ?>
                <div id="yelp-widget-pro-support" class="postbox">
                    <div class="handlediv" title="Click to toggle"><br></div><h3 class="hndle"><span>Need Support?</span></h3>
                    <div class="inside">
                    <p>If you have any problems with this plugin or ideas for improvements or enhancements, please use the <a href="http://wordpress.org/support/plugin/yelp-widget-pro" target="_blank">Support Forums</a>.</p>
                    </div><!-- /.inside -->
                </div><!-- /.yelp-widget-pro-support -->

            </div><!-- /.sidebar-sortables -->


        </div><!-- /.alignright -->
    </div><!-- /.metabox-holder -->
</form>


</div><!-- /#wrap -->

<?php
} //end yelp_widget_options_form
?>