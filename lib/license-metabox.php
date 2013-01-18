<?php
/**
 * @DESC: Licensing Metabox
 */

?>

<div id="yelp-widget-pro-premium" class="postbox">
    <div class="handlediv" title="Click to toggle"><br></div><h3 class="hndle"><span>Yelp Widget Pro Premium</span></h3>
    <div class="inside">

        <?php

        /*
         *  Premium License Logic
         *  (Stealing isn't nice... please respect and purchase a license rather than hacking)
         */

        //Check license logic
        $response = license_status($options);
        $options = get_option('yelp_widget_settings');

        //Current License Status
        $licenseStatus = $options["yelp_widget_premium_license_status"];

        //Activated
        $status = $response["activated"];
        $code = $response["code"];
        ?>

        <form id="yelp-license" method="post" action="options.php">

        <?php //Display appropriate notifications to the user
             echo license_response($response);  ?>

        <div class="control-group">
            <p>If you have purchased a license for Yelp Widget Pro Premium you may enter it in below to enable premium features:</p>
            <div class="control-label">
                  <label for="yelp_widget_premium_email">License Email</label>
            </div>

            <div class="controls">
                <input type="text" id="yelp_widget_premium_email" name="yelp_widget_settings[yelp_widget_premium_email]" placeholder="your.email@email.com" value="<?php echo yelp_widget_option('yelp_widget_premium_email', $options); ?>" />
                <!-- hidden license status field -->
                <input type="hidden" id="yelp_widget_premium_license_status" name="yelp_widget_settings[yelp_widget_premium_license_status]" value="<?php echo $licenseStatus;  ?>" />
            </div>
        </div><!--/.control-group -->
        <div class="control-group">
            <div class="control-label">
                <label for="yelp_widget_premium_license">License Key</label>
            </div>

            <div class="controls">
                <input type="text" id="yelp_widget_premium_license" name="yelp_widget_settings[yelp_widget_premium_license]" placeholder="VALID LICENSE KEY" value="<?php echo yelp_widget_option('yelp_widget_premium_license', $options); ?>"/>
            </div>

        </div><!--/.control-group -->


        <div class="control-group">
           <div class="controls">
               <?php
               //Output appropriate Submit Button
               if($licenseStatus == 1){ ?>

                   <input class="button" id="deactivate" type="submit" name="submit-button" value="<?php _e('Deactivate'); ?>" />

                <?php } else { ?>

                   <input class="button" id="activate" type="submit" name="submit-button" value="<?php _e('Activate'); ?>" />

                <?php } ?>

           </div>
       </div>


    </div><!-- /.inside -->
</div><!-- /.yelp-widget-pro-support -->