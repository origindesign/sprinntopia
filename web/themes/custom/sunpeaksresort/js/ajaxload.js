(function ($, Drupal, drupalSettings) {

    'use strict';


    Drupal.behaviors.ajaxListLoading = {};
    Drupal.behaviors.ajaxListLoading.xhr; // ajax call

    Drupal.behaviors.ajaxListLoading.attach = function (context, settings) {

        $('.ajax-list-container', context).once('ajaxListLoading').each(function () {

            var ajaxContainer = $(this);
            var hash = window.location.hash;
            var hashValue = hash.substring(1);
            var defaultHash = ajaxContainer.attr("data-default-hash");

            //console.log("Hash Value is: "+ typeof hashValue);

            // If there is no hash passed in URL and there is a default hash attribute set in listing, then use default
            if (typeof defaultHash !== typeof undefined && defaultHash !== false && hashValue == "" ) {
                hashValue = defaultHash;
                //console.log("New Hash Value is: "+hashValue);
            }



            if (ajaxContainer.length){
                var ajaxPath = ajaxContainer.attr("data-ajax-path");
                Drupal.behaviors.ajaxListLoading.displayList(ajaxContainer, ajaxPath, hashValue);
            }


        });


    };


    Drupal.behaviors.ajaxListLoading.displayList = function( $container, $path, $hash ){

        // Change Hash of URL
        window.location.hash = $hash;

        // Handle class for loading icon
        $container.html("").removeClass("loaded");

        // Define full path
        var fullPath = $path + "/" + $hash.replace(/\//g, '-');
		
        Drupal.behaviors.ajaxListLoading.xhr = $.ajax({
            type: 'GET',
            url: fullPath,
            data: { response_type: 'ajax' },
            success: function(result) {
                var wrapper = $( "<div/>", { "class": "wrapper"});
                wrapper.html(result);
                $container.html(wrapper);
                TweenMax.from( wrapper, 0.5, {opacity: 0});
                $container.addClass('loaded');
            }
        });

    }







})(jQuery, Drupal, drupalSettings);
