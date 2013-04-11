/**
 * Yelp Widget Pro Backend JavaScripts
 */

jQuery(function(){
    /*
     * Initialize the API Request Method widget radio input toggles
     */
     yelpWidgetToggles();

});

function yelpWidgetToggles() {

    //API Method Toggle
    jQuery('#widgets-right .widget-api-option input:not("clickable")').each(function() {

        jQuery(this).addClass("clickable").unbind("click").click(function () {
            jQuery(this).parent().parent().find('.toggled').slideUp().removeClass('toggled');
            if(jQuery(this).hasClass('search-api-option')) {
                jQuery(this).parent().next('.toggle-api-option-1').slideToggle().toggleClass('toggled');
            } else {
                jQuery(this).parent().next().next('.toggle-api-option-2').slideToggle().toggleClass('toggled');
            }


        });
    });

    //Advanced Options Toggle (Bottom-gray panels)
    jQuery('#widgets-right .yelp-toggler:not("clickable")').each(function () {

        jQuery(this).addClass("clickable").unbind("click").click(function () {
            jQuery(this).toggleClass('toggled');
            jQuery(this).next().slideToggle();
         })

    });



}


/*
 * Function to Refresh jQuery toggles for Yelp Widget Pro upon saving specific widget
 */
jQuery(document).ajaxSuccess(function(e, xhr, settings) {
    yelpWidgetToggles();

});