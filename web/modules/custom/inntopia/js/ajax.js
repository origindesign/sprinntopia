(function inntopiaAjaxScript($, Drupal) {

    'use strict';


    Drupal.behaviors.inntopiaAjaxAction = {};

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



    Drupal.behaviors.inntopiaAjaxAction.attach = function (context, settings) {

        $('body', context).once('inntopiaAjax').each(function () {

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

                // Add Package ID if it exists
                if ( $(this).data('packageid') ) {
                    productData.PackageId = $(this).data('packageid');
                }

                Drupal.behaviors.inntopiaAjaxAction.call( '/inntopia-ajax/actions/add-to-cart', productData, '/cart');

            });


            /**
             * Remove from cart, load remove-from-cart through ajax
             */
            $(".btn-remove-cart").on( "click", function(event) {

                event.preventDefault();
                event.stopPropagation();

                var itemContainer = $(this).parents(".cart-item");

                var itemData = {
                    'ItineraryItemId' : itemContainer.data('itineraryitemid')
                };

                Drupal.behaviors.inntopiaAjaxAction.call( '/inntopia-ajax/actions/remove-from-cart', itemData, '/cart');

            });

        });
    };







    Drupal.behaviors.inntopiaAjaxDisplay = {};

    Drupal.behaviors.inntopiaAjaxDisplay.attach = function (context, settings) {

        $('#ajaxContainer ', context).once('inntopiaAjax').each(function () {

            var ajaxPlaceholder = $(this).find('.ajaxContent');
            var ajaxPath = ajaxPlaceholder.data('ajax-url');
            var currentUrl = new URI(window.location.href);
            var params = currentUrl.search(true); // transform get string into POST object

            Drupal.behaviors.inntopiaAjaxDisplay.call( ajaxPlaceholder, ajaxPath, params );

        });

    };

    Drupal.behaviors.inntopiaAjaxDisplay.call = function( $container, $url, $params ){

        // Handle class for loading icon
        $container.html('').removeClass('loaded');

        var request = $.ajax({
            type: "GET",
            data: {data: $params},
            url: $url,
            success: function( result ){
                var resultHTML = $('<div />').append(result).find('#block-sunpeaksresort-content').html();
                $container.html(resultHTML);
                $container.addClass('loaded');
            }
        });

        request.fail(function() {
            console.log( "error" );
        });


    };







}(jQuery, Drupal));
