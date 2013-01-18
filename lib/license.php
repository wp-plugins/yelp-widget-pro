<?php
/**
 * @DESC: Yelp Widge Pro licensing logic
 */


//Function to check for premium license
function activate_license($options){

    $licence_key = $options['yelp_widget_premium_license'];
    $email = $options['yelp_widget_premium_email'];

    $args = array(
   		'wc-api'	  => 'software-api',
   		'request'     => 'activation',
   		'email'		  => $email,
   		'licence_key' => $licence_key,
   		'product_id'  => 'YELPWIDGETPROPREMIUM'
   	);

    //Execute request (function below)
    $result = execute_request($args);

    //If license is Activated
    if(!empty($result["activated"])){

        //Save transient variable to check license (saved as current UNIX timestamp)
        $licenseTransient = time();
        set_transient('yelp_widget_license_transient', $licenseTransient, 60 * 60 * 168);

        //Update option license status option
        $options['yelp_widget_premium_license_status'] = "1";
        update_option('yelp_widget_settings', $options);

        //Dl and unzip plugin now that license is activated
//        add_filter ('pre_set_site_transient_update_plugins', 'display_transient_update_plugins');

//        include ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
//        $upgrader = new Plugin_Upgrader();
//        return $upgrader->upgrade('yelp-widget-pro/yelp-widget-pro.php');

    }

    return $result;

}




// Valid deactivation reset request
function deactivate_license($options){

    $licence_key = $options['yelp_widget_premium_license'];
    $email = $options['yelp_widget_premium_email'];


	$args = array(
        'wc-api'	    => 'software-api',
		'request'       => 'deactivation',
		'email'         => $email,
		'licence_key'   => $licence_key,
		'instance'      => '',
		'product_id'  	=> 'YELPWIDGETPROPREMIUM'
    );

	$result = execute_request($args);

    if($result['reset'] == true){
        //Update option license status option and delete license
        $options['yelp_widget_premium_license_status'] = "0";
        $options['yelp_widget_premium_email'] = "";
        $options['yelp_widget_premium_license'] = "";
        update_option('yelp_widget_settings', $options);

    }

    return $result;

}


// Fire away!
function execute_request( $args ) {
    //Create request URL
	$target_url = create_url( $args );
    $target_url = html_entity_decode($target_url);

	$data = wp_remote_get($target_url);
    if( is_wp_error( $data ) ) {

        $message = "Something went wrong...";

    }   else {

        $message = $data['body'];

    }

    return json_decode($message, true);

}

// Create an url based on
function create_url( $args ) {
    $base_url = 'http://wordimpressed.local/';
	$base_url = add_query_arg( 'wc-api', 'software-api', $base_url );
	return $base_url . '&' . http_build_query( $args , '', '&amp;');
}


//Determine the status of this users license and apply applicable functions
function license_status($options){
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
         $response = activate_license($options);
    }
    //License is activated: 1
    elseif($licenseStatus == 1) {

        //Check license key
        $response = 'valid';


    }
    //User is deactivating license: 2
    elseif($licenseStatus == 2) {

        $response = deactivate_license($options);

    }

    return $response;

}

//Display License Responses to User
function license_response($response) {
    $status = $response["activated"];
    $code = $response["code"];

//    var_dump($status);
//    var_dump($code);
   // var_dump($response);

    //License is good and activated
    if(!empty($status) && $status == true || $response == 'valid') {
       $message = ($response['message'] != "v") ? ' <br/>'.$response['message'] : '';
       $response = '<div class="license-activated alert alert-success">
           <p><strong>License Activated</strong><br/> Thank you for purchasing Yelp Widget Pro Premium'.$message.'</p>
       </div>';
    }
    //License Key Errors
    elseif(!empty($code)) {

        switch($code) {
         case '101' :
             $error =  '<p><strong>License Invalid</strong><br/> Please check that the license you are using is valid.</p>';
             break;
         case '103' :
             $error = '<p><strong>License Invalid</strong><br/> Exceeded maximum number of activations.</p>';
             break;
         default :
             $error = '<p><strong>Invalid Request</strong><br/> Please <a href="http://wordpress.org/support/plugin/yelp-widget-pro" target="_blank">contact support</a> for assistance.</p>';
     }

     $response = '<div class="license-activated alert alert-red">'.$error.'</div>';


    }
    //Deactivated License Key
    elseif($response["reset"] == true) {

        $response = '<div class="license-deactivated alert alert-success">
                   <p><strong>License Deactivated</strong><br/> Thank you for purchasing Yelp Widget Pro Premium</p>
               </div>';

    }
    elseif(empty($response)) {

        $response = '<div class="no-license alert alert-info">
                   <p><strong>Upgrade to Yelp Widget Pro Premium</strong><br/> Features include review snippets, a shortcode to display Yelp businesses anywhere on your site, more configuration options and free lifetime updates.</p>
               </div>';


    } //endif

    return $response;


} //end license_response
