<?php
/*
Plugin Name: Yelp Widget Pro
Plugin URI: http://imdev.in/
Description: Easily display Yelp business ratings with a simple and intuitive WordPress widget.
Version: 1.0
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
// error_reporting(E_ALL);
// ini_set('display_errors', '1');


/**
 * Adds Yelp Widget Pro Options Page
 */
require_once (dirname (__FILE__) . '/options.php');
if(!class_exists('OAuthToken', false)) {
    require_once (dirname (__FILE__) . '/lib/oauth.php');
}

/**
 * Adds Yelp Widget Pro Stylesheets
 */
add_action('wp_print_styles', 'add_yelp_widget_css');

function add_yelp_widget_css() {

    $cssOption = get_option('yelp_widget_settings');

    if($cssOption["yelp_widget_disable_css"] == 0) {

        $url = WP_PLUGIN_URL . '/yelp-widget-pro/style/yelp.css';
        $dir = WP_PLUGIN_DIR . '/yelp-widget-pro/style/yelp.css';

        if (file_exists($dir)) {
            wp_register_style('yelp-widget', $url);
            wp_enqueue_style('yelp-widget');
        }
    }

}

/**
 * Adds Yelp Widget Pro Widget
 */

class Yelp_Widget extends WP_Widget {

    /**
   	 * Register widget with WordPress.
  	 */
   	public function __construct() {
   		parent::__construct(
   	 		'yelp_widget', // Base ID
   			'Yelp Widget Pro', // Name
   			array( 'description' => __( 'Display Yelp business ratings and reviews on your Website.', 'text_domain' ), ) // Args
   		);
   	}


    /**
   	 * Front-end display of widget.
   	 *
   	 * @see WP_Widget::widget()
   	 *
   	 * @param array $args   Widget arguments.
   	 * @param array $instance Saved values from database.
   	 */
    function widget($args, $instance) {

        extract( $args );

        /* Thanks Again to the Yelp It plugin for the following code! */
        $options = get_option('yelp_widget_settings'); // Retrieve settings array, if it exists

        // Base unsigned URL
        $unsigned_url = "http://api.yelp.com/v2/";

        // Token object built using the OAuth library
        $yelp_widget_token = $options['yelp_widget_token'];
        $yelp_widget_token_secret = $options['yelp_widget_token_secret'];

        $token = new OAuthToken($yelp_widget_token, $yelp_widget_token_secret);

        // Consumer object built using the OAuth library
        $yelp_widget_consumer_key = $options['yelp_widget_consumer_key'];
        $yelp_widget_consumer_secret = $options['yelp_widget_consumer_secret'];

        $consumer = new OAuthConsumer($yelp_widget_consumer_key, $yelp_widget_consumer_secret);

        // Yelp uses HMAC SHA1 encoding
        $signature_method = new OAuthSignatureMethod_HMAC_SHA1();

        // Yelp uses HMAC SHA1 encoding
        $signature_method = new OAuthSignatureMethod_HMAC_SHA1();

        //Yelp Widget Options
        $title              = apply_filters('widget_title', $instance['title']);
        $displayOption      = $instance['display_option'];
        $term               = $instance['term'];
        $id                 = $instance['id'];
        $location           = $instance['location'];
        $address            = $instance['display_address'];
        $limit              = $instance['limit'];
        $sort               = $instance['sort'];
        $align              = $instance['alignment'];
        $titleOutput        = $instance['disable_title_output'];
        $targetBlank        = $instance['target_blank'];
        $noFollow           = $instance['no_follow'];
        $cache              = $instance['cache'];

        //Build URL Parameters
        $urlparams = array(
            'term'             => $term,
            'id'               => $id,
            'location'         => $location,
            'limit'            => $limit,
            'sort'             => $sort
        );



        // If ID param is set, use business method and delete any other parameters
        if ($urlparams['id'] != '') {
            $urlparams['method'] = 'business/' . $urlparams['id'];
            unset($urlparams['term'], $urlparams['location'], $urlparams['id'], $urlparams['sort']);
        } else {
            $urlparams['method'] = 'search';
            unset($urlparams['id']);
        }

        // Set method
        $unsigned_url = $unsigned_url . $urlparams['method'];

        unset($urlparams['method']);

        // Build OAuth Request using the OAuth PHP library. Uses the consumer and
        // token object created above.
        $oauthrequest = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $unsigned_url, $urlparams);

        // Sign the request
        $oauthrequest->sign_request($signature_method, $consumer, $token);

        // Get the signed URL
        $signed_url = $oauthrequest->to_url();

        /* Debugging */
//         echo '<pre>';
//         print_r($signed_url);
//         echo '</pre>';

        // Cache: cache option is enabled
        if ($cache != 'None') {
            $transient = $term.$id.$location.$limit.$sort;
            // Check for an existing copy of our cached/transient data
            if(($response = get_transient($transient)) == false) {

                    //Get Time to Cache Data
                    $expiration = $cache;

                    //Assign Time to appropriate Math
                    switch($expiration) {

                        case "1 Hour":
                            $expiration =  3600;
                            break;
                        case "3 Hours":
                            $expiration = 3600 * 3;
                            break;
                        case "6 Hours":
                            $expiration = 3600 * 6;
                            break;
                        case "12 Hours":
                            $expiration = 60 * 60 * 12;
                            break;
                        case "1 Day":
                            $expiration = 60 * 60 * 24;
                            break;
                        case "2 Days":
                            $expiration = 60 * 60 * 48;
                            break;
                        case "1 Week":
                            $expiration = 60 * 60 * 168;
                            break;


                    }

                   // Cache data wasn't there, so regenerate the data and save the transient
                   $response = yelp_widget_curl($signed_url);
                   set_transient( $transient, $response, $expiration);

//               var_dump($urlparams);
//               var_dump($response);
//               die('here2');


            };

        }  else {

            //No Cache option enabled;
            $response = yelp_widget_curl($signed_url);

        }



        /*
         * Output Yelp Widget Pro
         */

        /* Debugging */
//        echo '<pre>';
//        var_dump($response);
//        echo '</pre>';

         //Widget Output
        echo $before_widget;

        // if the title is set & the user hasn't disabled title output
        if ( $title && $titleOutput != 1 ) {
            echo $before_title . $title . $after_title;
        }

        if (isset($response->businesses)) {
               $businesses = $response->businesses;
           } else {
               $businesses = array($response);
           }

           // Instantiate output var
           $output = '';

           if (isset($response->error)) {
               $output = '<div class="yelp-error">';
               if ($response->error->id == 'EXCEEDED_REQS') {
                   $output .= 'Yelp is exhausted (Contact Yelp to increase your API call limit)';
               } else {
                   $output .= $response->error->text;
               }

               $output .='</div>';
           } else if (!isset($businesses[0])) {
               $output = '<div class="yelp-error">No results</div>';
           } else {


               // Open link in new window if set
               if ($targetBlank == 1) {
                   $targetBlank = 'target="_blank" ';
               } else {
                   $targetBlank = '';
               }
               // Add nofollow relation if set
               if ($noFollow == 1) {
                   $noFollow = 'rel="nofollow" ';
               } else {
                   $noFollow = '';
               }

               //Begin Setting Output Variable by Looping Data from Yelp
               for ( $x = 0; $x < count($businesses); $x++) {
                   $output .= '<div class="yelp yelp-business '. $align .'">'
                           . '<div class="biz-img-wrap"><img class="picture" src="'
                               . esc_attr($businesses[$x]->image_url) . '" /></div>'
                           . '<div class="info">'
                               . '<a class="name" '
                                   . $targetBlank
                                   . $noFollow
                                   . 'href="'
                                   . esc_attr($businesses[$x]->url) . '" title="'
                                   . esc_attr($businesses[$x]->name) . ' Yelp page">'
                                   . $businesses[$x]->name

                               . '</a>'
                               . '<img class="rating" src="'
                                   . esc_attr($businesses[$x]->rating_img_url)
                                   . '" alt="" title="Yelp Rating" />'
                               . '<span class="review-count">'
                                   . esc_attr($businesses[$x]->review_count)
                                   . ' reviews'
                               . '</span>'
                               . '<a class="yelp-branding" href="http://www.yelp.com"'
                                   . $targetBlank
                                   . $noFollow .'>
                                   <img src="' . WP_PLUGIN_URL . '/yelp-widget-pro/style/yelp.png" alt="powered by Yelp" />'
                               . '</a></div>';


                               //Does the User want to display Address?
                               if($address == 1) {
                                   $output .=  '<div class="yelp-address-wrap"><address>';

                                   //Itterate through Address Array
                                   foreach($businesses[$x]->location->display_address as $addressItem){

                                          $output .= $addressItem."<br/>";
                                   }

                                   $output .=  '<address></div>';

                               }
                        //Continue Setting Output Variable
                        $output .= '</div>';

               }
           }
           //Output Widget Contents
           echo $output;

           echo $after_widget;

    }


    /**
     * @DESC: Saves the widget options
     * @SEE WP_Widget::update */
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title']                = strip_tags($new_instance['title']);
        $instance['display_option']       = strip_tags($new_instance['display_option']);
        $instance['term']                 = strip_tags($new_instance['term']);
        $instance['id']                   = strip_tags($new_instance['id']);
        $instance['location']             = strip_tags($new_instance['location']);
        $instance['display_address']      = strip_tags($new_instance['display_address']);
        $instance['limit']                = strip_tags($new_instance['limit']);
        $instance['sort']                 = strip_tags($new_instance['sort']);
        $instance['alignment']            = strip_tags($new_instance['alignment']);
        $instance['disable_title_output'] = strip_tags($new_instance['disable_title_output']);
        $instance['target_blank']         = strip_tags($new_instance['target_blank']);
        $instance['no_follow']            = strip_tags($new_instance['no_follow']);
        $instance['cache']                = strip_tags($new_instance['cache']);
        return $instance;
    }


   /**
    * Back-end widget form.
    * @see WP_Widget::form()
    */
   function form($instance) {

        $title          = esc_attr($instance['title']);
        $displayOption  = esc_attr($instance['display_option']);
        $term           = esc_attr($instance['term']);
        $id             = esc_attr($instance['id']);
        $location       = esc_attr($instance['location']);
        $address        = esc_attr($instance['display_address']);
        $limit          = esc_attr($instance['limit']);
        $sort           = esc_attr($instance['sort']);
        $align          = esc_attr($instance['alignment']);
        $titleOutput    = esc_attr($instance['disable_title_output']);
        $targetBlank    = esc_attr($instance['target_blank']);
        $noFollow       = esc_attr($instance['no_follow']);
        $cache          = esc_attr($instance['cache']);

        $apiOptions = get_option('yelp_widget_settings');

        //Verify that the API values have been inputed prior to output
        if(empty($apiOptions["yelp_widget_consumer_key"]) || empty($apiOptions["yelp_widget_consumer_secret"]) || empty($apiOptions["yelp_widget_token"]) || empty($apiOptions["yelp_widget_token_secret"])) {
        //the user has not properly configured plugin so diplay a warning
        ?>
            <div class="alert alert-red">Please input your Yelp API information in the <a href="options-general.php?page=yelp_widget">plugin settings</a> page prior to enabling Yelp Widget Pro.</div>
        <?php }
        //The user has properly inputted Yelp API info so output widget form so output the widget contents
        else {
        ?>

        <!-- Title -->
         <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
         </p>

        <!-- Listing Options -->
        <p class="widget-api-option">
            <label for="<?php echo $this->get_field_id('display_option'); ?>"><?php _e('Yelp API Request Method:'); ?></label><br />
            <input type="radio" name="<?php echo $this->get_field_name('display_option'); ?>" class="<?php echo $this->get_field_id('display_option'); ?>" value="0" <?php checked('0', $displayOption); ?>><span>Search Method</span><br />
            <input type="radio" name="<?php echo $this->get_field_name('display_option'); ?>" class="<?php echo $this->get_field_id('display_option'); ?>" value="1" <?php checked('1', $displayOption); ?>><span>Business Method</span>
        </p>

        <div class="toggle-api-option-1 toggle-item <?php if($displayOption == 0) { echo 'toggled'; } ?>">
         <!-- Search Term -->
         <p>
            <label for="<?php echo $this->get_field_id('term'); ?>"><?php _e('Search Term:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('term'); ?>" name="<?php echo $this->get_field_name('term'); ?>" type="text" value="<?php echo $term; ?>" />
         </p>



         <!-- Location -->
         <p>
            <label for="<?php echo $this->get_field_id('location'); ?>"><?php _e('Location:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('location'); ?>" name="<?php echo $this->get_field_name('location'); ?>" type="text" value="<?php echo $location; ?>" />
         </p>

        <!-- Limit -->
        <p>
            <label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('Number of Items:'); ?></label>
            <select name="<?php echo $this->get_field_name('limit'); ?>" id="<?php echo $this->get_field_id('limit'); ?>" class="widefat">
                <?php
                $options = array('1', '2', '3','4','5','6','7','8','9','10');
                foreach ($options as $option) {
                    echo '<option value="' . $option . '" id="' . $option . '"', $limit == $option ? ' selected="selected"' : '', '>', $option, '</option>';
                }
                ?>
            </select>
        </p>

        <!-- Sort -->
        <p>
            <label for="<?php echo $this->get_field_id('sort'); ?>"><?php _e('Sorting:'); ?></label>
            <select name="<?php echo $this->get_field_name('sort'); ?>" id="<?php echo $this->get_field_id('sort'); ?>" class="widefat">
                <?php
                $options = array('Best Match','Distance','Highest Rated');
                //Counter for Option Values
                $counter = 0;

                foreach ($options as $option) {
                    echo '<option value="' . $counter . '" id="' . $option . '"', $sort == $option ? ' selected="selected"' : '', '>', $option, '</option>';
                    $counter++;
                }
                ?>
            </select>
        </p>

        </div><!-- /.toggle-api-option-1 -->


        <div class="toggle-api-option-2 toggle-item  <?php if($displayOption == 1) { echo 'toggled'; } ?>">
           <!-- Business ID -->
           <p>
             <label for="<?php echo $this->get_field_id('id'); ?>"><?php _e('Business ID:'); ?></label>
             <input class="widefat" id="<?php echo $this->get_field_id('id'); ?>" name="<?php echo $this->get_field_name('id'); ?>" type="text" value="<?php echo $id; ?>" />
           </p>
        </div>


        <h4 class="yelp-toggler">Display Options: <span></span></h4>

        <div class="display-options toggle-item">


            <!-- Disable title output checkbox -->
            <p>
              <input id="<?php echo $this->get_field_id('display_address'); ?>" name="<?php echo $this->get_field_name('display_address'); ?>" type="checkbox" value="1" <?php checked( '1', $address ); ?>/>
              <label for="<?php echo $this->get_field_id('display_address'); ?>"><?php _e('Display Business Address'); ?></label>
           </p>

        </div>

        <h4 class="yelp-toggler">Advanced Options: <span></span></h4>

        <div class="advanced-options toggle-item">

                <!-- Disable title output checkbox -->
                <p>
                   <input id="<?php echo $this->get_field_id('disable_title_output'); ?>" name="<?php echo $this->get_field_name('disable_title_output'); ?>" type="checkbox" value="1" <?php checked( '1', $titleOutput ); ?>/>
                   <label for="<?php echo $this->get_field_id('disable_title_output'); ?>"><?php _e('Disable Title Output'); ?></label>
               </p>

                <!-- Open Links in New Window -->
                <p>
                   <input id="<?php echo $this->get_field_id('target_blank'); ?>" name="<?php echo $this->get_field_name('target_blank'); ?>" type="checkbox" value="1" <?php checked( '1', $targetBlank ); ?>/>
                   <label for="<?php echo $this->get_field_id('target_blank'); ?>"><?php _e('Open Links in New Window'); ?></label>
               </p>
                <!-- No Follow Links -->
                <p>
                   <input id="<?php echo $this->get_field_id('no_follow'); ?>" name="<?php echo $this->get_field_name('no_follow'); ?>" type="checkbox" value="1" <?php checked( '1', $noFollow ); ?>/>
                   <label for="<?php echo $this->get_field_id('no_follow'); ?>"><?php _e('No Follow Links'); ?></label>
               </p>

               <!-- Transient / Cache -->
               <p>
                   <label for="<?php echo $this->get_field_id('cache'); ?>"><?php _e('Cache Data:'); ?></label>
                   <select name="<?php echo $this->get_field_name('cache'); ?>" id="<?php echo $this->get_field_id('cache'); ?>" class="widefat">
                       <?php
                       $options = array('None', '1 Hour','3 Hours','6 Hours', '12 Hours', '1 Day', '2 Days', '1 Week');

                       foreach ($options as $option) {
                           echo '<option value="' . $option . '" id="' . $option . '"', $cache == $option ? ' selected="selected"' : '', '>', $option, '</option>';
                           $counter++;
                       }
                       ?>
                   </select>
               </p>


        </div>

        <?php  } //endif check for Yelp API key inputs ?>

           <?php
       }  //end form function

}

/**
 * @DESC: CURLs the Yelp API with our url parameters and returns JSON response
 */

function yelp_widget_curl($signed_url){
    // Send Yelp API Call
    $ch = curl_init($signed_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $data = curl_exec($ch); // Yelp response
    curl_close($ch);

    // Handle Yelp response data
    $response = json_decode($data);

    return $response;

}


/*
 * @DESC: Register Twitter Widget Pro widget
 */
add_action( 'widgets_init', create_function( '', 'register_widget( "Yelp_Widget" );' ) );