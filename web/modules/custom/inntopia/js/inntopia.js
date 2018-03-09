(function inntopiaScript($, Drupal) {

    'use strict';


    Drupal.behaviors.inntopia = {};
    Drupal.behaviors.inntopia.pageContainer = $('#pageContainer');
    Drupal.behaviors.inntopia.header = $('#header');
    Drupal.behaviors.inntopia.quickCart = $('#quickCart');
    Drupal.behaviors.inntopia.attach = function (context, settings) {

        $('body', context).once('inntopia').each(function () {


            // Add Datepicker on check in/out input
            $("#arrivalDate").datepicker({
                dateFormat: "yy-mm-dd",
                changeMonth: true,
                changeYear: true,
                showAnim: "fadeIn",
                yearRange: "c:c+2",
                onSelect: function( selectedDate ) {
                    var dayAfter =  moment(selectedDate, "YYYY-MM-DD").add(1, 'd');
                    $("#departureDate").datepicker( "option", "minDate", dayAfter.format("YYYY-MM-DD") );
                    $(this).change();
                }
            });
            $("#departureDate").datepicker({
                dateFormat: "yy-mm-dd",
                changeMonth: true,
                changeYear: true,
                showAnim: "fadeIn",
                yearRange: "c:c+2",
            });


            $(".input-select").each(function( index ) {
                var defaultValue = $(this).data('defaultvalue');
                $(this).val(defaultValue).trigger("change");
            });



            // Opening Cart
            $("#cartBtn").on( "click", function(event) {

                event.preventDefault();
                event.stopPropagation();

                if ( !Drupal.behaviors.inntopia.pageContainer.hasClass('slided') ){
                    Drupal.behaviors.inntopia.openCart();
                }else{
                    Drupal.behaviors.inntopia.closeCart();
                }
            });


            // Closing Cart
            $("#quickCart .close").on( "click", function(event) {
                event.preventDefault();
                event.stopPropagation();
                Drupal.behaviors.inntopia.closeCart();
            });

        });


        // Element outside of once (that need to be triggered after page load)


        // Slick thumb Carousel
        $('.carousel-thumb.hasSlider').slick({
            slide: '.image',
            dots: true,
            adaptiveHeight: true
        });



    };



    Drupal.behaviors.inntopia.openCart = function(){

        Drupal.behaviors.inntopia.pageContainer.addClass('slided');
        Drupal.behaviors.inntopia.header.addClass('slided');
        Drupal.behaviors.inntopia.quickCart.addClass('slided');
        $('body').addClass('noscroll');

    };


    Drupal.behaviors.inntopia.closeCart = function(){

        Drupal.behaviors.inntopia.pageContainer.removeClass('slided');
        Drupal.behaviors.inntopia.header.removeClass('slided');
        Drupal.behaviors.inntopia.quickCart.removeClass('slided');
        $('body').removeClass('noscroll');

    };



}(jQuery, Drupal));
