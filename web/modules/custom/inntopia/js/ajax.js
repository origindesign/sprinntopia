(function inntopiaAjaxScript($, Drupal) {

    'use strict';




    /**
     * AJAX ACTIONS
     *
     */
    Drupal.behaviors.inntopiaAjaxAction = {};

    /*
    Drupal.behaviors.inntopiaAjaxAction.call = function( $url, $params, $redirectUrl ){

        var request = $.ajax({
            type: "POST",
            data: {data: $params},
            url: $url,
            success: function( result ){
                //console.log( "success" );
                console.log( result );
            }
        });

        request.done(function( result ) {
            window.location.href = $redirectUrl;
        });

        request.fail(function() {
            console.log( "error" );
        });


    };
    */



    Drupal.behaviors.inntopiaAjaxAction.attach = function (context, settings) {

        $('body', context).once('inntopiaAjaxAction').each(function () {

            /**
             * Add to cart, load add-to-cart through ajax
             */
            $(".product-action").on( "click", function(event) {

                event.preventDefault();
                event.stopPropagation();

                var productContainer = $(this).parents(".product-data");

                var productData = {
                    'SupplierId' : productContainer.data('supplierid'),
                    'ProductId' : productContainer.data('productid'),
                    'ArrivalDate' : productContainer.data('arrivaldate'),
                    'DepartureDate' : productContainer.data('departuredate'),
                    'AdultCount' : productContainer.data('adultcount'),
                    'ChildCount' : productContainer.data('childcount'),
                    "Quantity" : productContainer.data('quantity')
                };

                console.log(productData);

                // Add Package ID if it exists
                if ( $(this).data('packageid') ) {
                    productData.PackageId = $(this).data('packageid');
                }

                Drupal.behaviors.inntopiaAjaxAction.addToCart( productData );

            });


            /**
             * Remove from cart, load remove-from-cart through ajax
             */
            $( "#quickCart" ).on( "click", ".btn-remove-cart", function(event) {

                event.preventDefault();
                event.stopPropagation();

                var itemContainer = $(this).parents(".cart-item");

                var itemData = {
                    'ItineraryItemId' : itemContainer.data('itineraryitemid')
                };

                Drupal.behaviors.inntopiaAjaxAction.removeFromCart( itemData );

            });

        });
    };



    Drupal.behaviors.inntopiaAjaxAction.removeFromCart = function( $data ){

        var $url = '/inntopia-ajax/actions/remove-from-cart';
        var ajaxPlaceholder = Drupal.behaviors.inntopia.quickCart.find('.ajaxContent');

        ajaxPlaceholder.addClass('loading');

        var request = $.ajax({
            type: "POST",
            data: {data: $data},
            url: $url,
            success: function( result ){
                //console.log( result );
            }
        });

        request.done(function( result ) {
            // Empty quick cart before adding the refreshed one
            var ajaxPlaceholder = Drupal.behaviors.inntopia.quickCart.find('.ajaxContent');
            Drupal.behaviors.inntopiaAjaxDisplay.call( ajaxPlaceholder, '/inntopia-ajax/display/display-quickcart', null );
        });

        request.fail(function() {
            console.log( "Error in ajax actions." );
        });

    };





    Drupal.behaviors.inntopiaAjaxAction.addToCart = function( $data){

        var $url = '/inntopia-ajax/actions/add-to-cart';
        var ajaxPlaceholder = Drupal.behaviors.inntopia.quickCart.find('.ajaxContent');

        ajaxPlaceholder.addClass('loading');

        // Open Quick Cart
        Drupal.behaviors.inntopia.openCart();

        var request = $.ajax({
            type: "POST",
            data: {data: $data},
            url: $url,
            success: function( result ){
                //console.log( result );
            }
        });

        request.done(function( result ) {
            // Empty quick cart before adding the refreshed one
            var ajaxPlaceholder = Drupal.behaviors.inntopia.quickCart.find('.ajaxContent');
            Drupal.behaviors.inntopiaAjaxDisplay.call( ajaxPlaceholder, '/inntopia-ajax/display/display-quickcart', null );
        });

        request.fail(function() {
            console.log( "Error in ajax actions." );
        });


    };








    /**
     * AJAX DISPLAY
     *
     */
    Drupal.behaviors.inntopiaAjaxDisplay = {};

    Drupal.behaviors.inntopiaAjaxDisplay.attach = function (context, settings) {

        $('body', context).once('inntopiaAjaxDisplay').each(function () {

            $(".ajaxContainer").each(function( index ) {

                var ajaxPlaceholder = $(this).find('.ajaxContent');
                var ajaxPath = ajaxPlaceholder.data('ajax-url');
                var currentUrl = new URI(window.location.href);
                var params = currentUrl.search(true); // transform get string into POST object

                Drupal.behaviors.inntopiaAjaxDisplay.call( ajaxPlaceholder, ajaxPath, params );

            });
        });

    };

    Drupal.behaviors.inntopiaAjaxDisplay.call = function( $container, $url, $params ){

        $container.addClass('loading');

        var request = $.ajax({
            type: "GET",
            data: {data: $params},
            url: $url
        });

        request.done(function( result ) {
            // Handle class for loading icon
            $container.html('');
            var resultDiv = $('<div />').append(result).find('#block-sunpeaksresort-content');
            var resultHTML = resultDiv.html();

            // Load html result in container
            $container.html(resultHTML);

            // Relaunch non unique behaviors
            Drupal.attachBehaviors();

            // Remove Loading class
            $container.removeClass('loading');

            // Update cart if the call is cart related
            var cartDiv = $container.find('.cart');
            if( cartDiv.length > 0 ){
                Drupal.behaviors.inntopiaAjaxDisplay.displayNbCartItems( cartDiv.data("nb-items") );
            }

            // Init Isotope for listing if the call is listing related
            var listingDiv = $container.find('.result-list');
            if( listingDiv.length > 0 ) {
                // Set a small delay to load isotope so we're sure it's loaded and displayed
                setTimeout(function () {
                    Drupal.behaviors.inntopia.isotope.init(listingDiv);
                }, 250);
            }


        });



        request.fail(function() {
            console.log( "error" );
        });


    };





    Drupal.behaviors.inntopiaAjaxDisplay.displayNbCartItems = function( $nb ){
        if ( $nb !== undefined && $nb !== 0){
            $('#cartBtn .number').removeClass('empty').text($nb);
        }else{
            $('#cartBtn .number').addClass('empty').text('');
        }
    };









}(jQuery, Drupal));
