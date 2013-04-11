/**
 * @DESC: Controls Yelp Widget Pro options panel
 */

jQuery(function(){

    //Deactivate License
    jQuery('#deactivate').on('click', function(e){

        //Prevent default form submit
        e.preventDefault();

        //Update license status value
        jQuery('#yelp_widget_premium_license_status').val('2');

        //Submit form
        jQuery('#yelp-settings').submit();

    });

});
