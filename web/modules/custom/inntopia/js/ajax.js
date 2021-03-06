(function inntopiaAjaxScript($, Drupal) {

    'use strict';




    /**
     * AJAX ACTIONS
     *
     */
    Drupal.behaviors.inntopiaAjaxAction = {};


    Drupal.behaviors.inntopiaAjaxAction.checkoutCall = function( $url, $params, $redirectUrl ){

        var request = $.ajax({
            type: "POST",
            data: {data: $params},
            url: $url,
            success: function( result ){
                /**
                 * @TODO add script to check message and redirect if success or display error message if error
                 */
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

        $('body', context).once('inntopiaAjaxAction').each(function () {

            /**
             * Add to cart, load add-to-cart through ajax
             */
            $(".product-action").on( "click", function(event) {

                event.preventDefault();
                event.stopPropagation();

                var packageComponents = false;
                var productData = {package: false, data: [] };
                var isPackage = false;

                // Determine if it's a package or not
                if ( $(this).data('packageid') && $(this).data('roomid') ) {
                    packageComponents = $('#package-'+ $(this).data('packageid')+'-'+$(this).data('roomid'));
                    if( packageComponents.length ){
                        isPackage = true;
                        productData.package = $(this).data('packageid');
                    }
                }


                // If it's a package, loop through the component and add them, otherwise add classic product data
                if ( isPackage && packageComponents ) {

                    packageComponents.find('.component').each(function( index ) {

                        var componentItem = $(this);

                        var componentData = {
                            'SupplierId' : componentItem.data('supplierid'),
                            'ProductId' : componentItem.data('productid'),
                            'ArrivalDate' : componentItem.data('arrivaldate'),
                            'DepartureDate' : componentItem.data('departuredate'),
                            'AdultCount' : componentItem.data('adultcount'),
                            'ChildCount' : componentItem.data('childcount'),
                            "Quantity" : parseInt(componentItem.data('quantity')),
                            "PackageComponentId" : componentItem.data('packagecomponentid')
                        };


                        if(componentData.Quantity !== 0){
                            productData.data.push(componentData);
                        }

                    });



                }else{

                    var productContainer = $(this).parents(".product-data");
                    var quantityField = $(this).parents("tr").find('input[name="quantity"]');


                    // If it's an option, first update the parent data with the option/product ID
                    if ( $(this).hasClass('product-option') ){
                        var productId = $(this).data('option-id');
                        productContainer.data('productid', productId);

                    }

                    // If the quantity field exists, update the parent data with the quantity number
                    if( quantityField !== undefined ){
                        var quantity = quantityField.val();
                        productContainer.data('quantity', quantity);
                    }


                    productData.data = [{
                        'SupplierId' : productContainer.data('supplierid'),
                        'ProductId' : productContainer.data('productid'),
                        'ArrivalDate' : productContainer.data('arrivaldate'),
                        'DepartureDate' : productContainer.data('departuredate'),
                        'AdultCount' : productContainer.data('adultcount'),
                        'ChildCount' : productContainer.data('childcount'),
                        "Quantity" : productContainer.data('quantity')
                    }];


                }

                Drupal.behaviors.inntopiaAjaxAction.addToCart( productData );

            });


            /**
             * Remove from cart, load remove-from-cart through ajax
             */
            $("#quickCart").on( "click", ".btn-remove-cart", function(event) {

                event.preventDefault();
                event.stopPropagation();

                var itemContainer = $(this).parents(".cart-item");

                var itemData = {
                    'ItineraryItemId' : itemContainer.data('itineraryitemid')
                };

                Drupal.behaviors.inntopiaAjaxAction.removeFromCart( itemData );

            });


            /**
             * Checkout Step submit form actions
             */
            $('.form-checkout').submit(function( event ) {

                event.preventDefault();

                var params = {};
                var submitBtn = $(this).find('input[type=submit]');
                var postUrl = submitBtn.data('post-url');
                var redirectUrl = submitBtn.data('redirect-url');

                // Loop through all the input/select with a name attribute and add it into an array of params
                $('.form-checkout input:not([type=submit]), .form-checkout select').each(
                    function(index){
                        var input = $(this);
                        var attr = input.attr('name');
                        if (typeof attr !== typeof undefined && attr !== false) {
                            params[attr] = input.val();
                        }
                    }
                );

                Drupal.behaviors.inntopiaAjaxAction.checkoutCall( postUrl, params, redirectUrl );

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
                console.log( result );
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
                Drupal.behaviors.inntopia.isotope.init(listingDiv);
            }

            // Init Map fo listing if the call has a map item
            var mapDiv = $container.find('.result-map');
            if( mapDiv.length > 0 ) {
                Drupal.behaviors.inntopiaMaps.init(mapDiv);
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
