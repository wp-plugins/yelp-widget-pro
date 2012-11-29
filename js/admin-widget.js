/**
 * Yelp Widget Pro Backend JavaScripts
 */

jQuery(function(){
    toggleAPImethod();
});

function toggleAPImethod() {

    //API Method Toggle
    jQuery('#widgets-right .widget-api-option input:not("clickable")').each(function() {

        jQuery(this).addClass("clickable").unbind("click").click(function () {
            jQuery(this).parent().next('.toggle-api-option-1').slideToggle().toggleClass('toggled');
            jQuery(this).parent().next().next('.toggle-api-option-2').slideToggle().toggleClass('toggled');
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
        toggleAPImethod();
});
