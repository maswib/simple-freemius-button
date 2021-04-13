jQuery(document).ready(function($) {
    "use strict";
    
    var fs_handler = FS.Checkout.configure({
        currency   : Simple_Freemius_Button.currency,
        plugin_id  : Simple_Freemius_Button.plugin_id,
        plan_id    : Simple_Freemius_Button.plan_id,
        public_key : Simple_Freemius_Button.public_key
    });
    
    $(document).on('click', Simple_Freemius_Button.buy_button_selector, function (e) {
        var target = $(this).attr('href'); // Example: #buy-5 => 5-Sites Licenses
        var length = parseInt(Simple_Freemius_Button.buy_button_link_prefix.length);
        var license_id = target.substr(length);
        
        fs_handler.open({
            licenses : license_id,
            purchaseCompleted : function (response) {},
            success : function (response) {},
            trial : false
        });
        
        e.preventDefault();
    });
    
    $(document).on('click', Simple_Freemius_Button.free_trial_selector, function (e) {
        var target = $(this).attr('href'); // Example: #trial-5 => 5-Sites Licenses
        var length = parseInt(Simple_Freemius_Button.free_trial_link_prefix.length);
        var license_id = target.substr(length);
        
        fs_handler.open({
            licenses : license_id,
            purchaseCompleted : function (response) {},
            success : function (response) {},
            trial : true
        });
        
        e.preventDefault();
    });
    
});