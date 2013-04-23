<?php
/**
 * @DESC: Yelp Widget Pro licensing logic
 */


if ( !class_exists('Plugin_Licensing') ):

class Plugin_Licensing {

    private $plugin = 'yelp-widget-pro/yelp-widget-pro.php';
    private $base_url = 'http://wordimpress.com/';
    private $opensource = 'http://downloads.wordpress.org/plugin/yelp-widget-pro.1.3.5.zip';
    private $premium    = 'http://wordimpress.com/downloads/files/yelp-widget-pro.zip';
    private $productID = 'YELPWIDGETPRO';


    //public function to check for premium license
    public function activate_license($options){

        $licence_key = $options['yelp_widget_premium_license'];
        $email = $options['yelp_widget_premium_email'];

        $args = array(
       		'wc-api'	  => 'software-api',
       		'request'     => 'activation',
       		'email'		  => $email,
       		'licence_key' => $licence_key,
       		'product_id'  => $this->productID
       	);

        //Execute request (function below)
        $result = Plugin_Licensing::execute_request($args);

        //If license is Activated
        if(!empty($result["activated"])){

            //Save transient variable to check license (saved as current UNIX timestamp)
            $licenseTransient = time();
            set_transient('yelp_widget_license_transient', $licenseTransient, 60 * 60 * 168);

            //Update option license status option
            $options['yelp_widget_premium_license_status'] = "1";
            update_option('yelp_widget_settings', $options);


            //Run Upgrade Func
            Plugin_Licensing::upgrade_downgrade($this->premium);

        }

        return $result;

    }

    // Valid deactivation reset request
    public function deactivate_license($options){

        $licence_key = $options['yelp_widget_premium_license'];
        $email = $options['yelp_widget_premium_email'];

    	$args = array(
            'wc-api'	    => 'software-api',
    		'request'       => 'deactivation',
    		'email'         => $email,
    		'licence_key'   => $licence_key,
    		'instance'      => '',
    		'product_id'  	=> $this->productID
        );

    	$result = Plugin_Licensing::execute_request($args);

        if($result['reset'] == true){
            //Update option license status option and delete license
            $options['yelp_widget_premium_license_status'] = "0";
            $options['yelp_widget_premium_email'] = "";
            $options['yelp_widget_premium_license'] = "";
            update_option('yelp_widget_settings', $options);

            //Run Upgrade Function
            Plugin_Licensing::upgrade_downgrade($this->opensource);

        }

        return $result;

    }

    //Check License
    public function check_license(){




    }


    // Fire away!
    public function execute_request( $args ) {
        //Create request URL
    	$target_url = Plugin_Licensing::create_url( $args );
        $target_url = html_entity_decode($target_url);

        //get data from target_url using WP's built in function
    	$data = wp_remote_get($target_url);

        //Handle response
        if( is_wp_error( $data ) ) {

            $message = "Something went wrong...";

        }   else {

            $message = $data['body'];

        }

        //Return JSON decoded response
        return json_decode($message, true);

    }

    // Create an url (used for License activation)
    public function create_url( $args ) {
    	$base_url = add_query_arg( 'wc-api', 'software-api', $this->base_url );
    	return $base_url . '&' . http_build_query( $args , '', '&amp;');
    }


    //Determine the status of this users license and apply applicable functions
    public function license_status($options){

        //grab the license data from the plugin options
        $licenseStatus = $options["yelp_widget_premium_license_status"];
        $licenseKey = $options['yelp_widget_premium_license'];
        $licenseEmail = $options['yelp_widget_premium_email'];
        $response = '';

        /*
         *  Newly activated user: 0
         *   if the user has not activated their license ever before
         *   and has inserted an email and license key
         */
        if($licenseStatus == 0 && !empty($licenseKey) && !empty($licenseEmail)) {
             $response = Plugin_Licensing::activate_license($options);
        }
        //License is activated: 1
        elseif($licenseStatus == 1) {

            //Check license key
            $response = 'valid';


        }
        //User is deactivating license: 2
        elseif($licenseStatus == 2) {

            $response = Plugin_Licensing::deactivate_license($options);

        }

        return $response;

    }

    //Display License Responses to User
    public function license_response($response) {
        $status = $response["activated"];
        $code = $response["code"];

        //License is good and activated
        if(!empty($status) && $status == true || $response == 'valid') {
           $message = ($response['message'] != "v") ? ' <br/>'.$response['message'] : '';
           $response = __('<div class="license-activated alert alert-success">
               <p><strong>License Activated</strong><br/> Thank you for purchasing Yelp Widget Pro Premium'.$message.'</p>
           </div>','ywp');
        }
        //License Key Errors
        elseif(!empty($code)) {

            switch($code) {
             case '101' :
                 $error =  __('<p><strong>License Invalid</strong><br/> Please check that the license you are using is valid.</p>','ywp');
                 break;
             case '103' :
                 $error = __('<p><strong>License Invalid</strong><br/> Exceeded maximum number of activations.</p>','ywp');
                 break;
             default :
                 $error = __('<p><strong>Invalid Request</strong><br/> Please <a href="http://wordpress.org/support/plugin/yelp-widget-pro" target="_blank">contact support</a> for assistance.</p>', 'ywp');
         }

         $response = '<div class="license-activated alert alert-red">'.$error.'</div>';


        }
        //Deactivated License Key
        elseif($response["reset"] == true) {

            $response = '<div class="license-deactivated alert alert-success">
                       <p><strong>'. __('License Deactivated</strong><br/> Thank you for using Yelp Widget Pro Premium', 'ywp') .'</p>
                   </div>';

        }
        elseif(empty($response)) {

            $response = '<div class="no-license alert alert-info">
                       <p><strong>'. __('Upgrade to Yelp Widget Pro Premium</strong><br/> Features include review snippets, a shortcode to display Yelp businesses anywhere on your site, more configuration options and free lifetime updates.','ywp').'</p>
                   </div>';


        } //endif

        return $response;


    } //end license_response

    private function upgrade_downgrade($package){

        include ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
          $upgrader = new Plugin_Upgrader();

          $upgrader->init();
          $upgrader->install_strings();
          $upgrader->upgrade_strings();
          $upgrader->run(array(
          					'package' => $package,
          					'destination' => WP_PLUGIN_DIR,
          					'clear_destination' => true,
          					'clear_working' => true,
          					'hook_extra' => array(
          								'plugin' => $this->plugin
          					)
          				));

    }

}

endif;