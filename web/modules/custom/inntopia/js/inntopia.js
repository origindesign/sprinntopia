(function inntopiaScript($, Drupal) {

    'use strict';


    Drupal.behaviors.inntopia = {};
    Drupal.behaviors.inntopia.pageContainer = $('#pageContainer');
    Drupal.behaviors.inntopia.header = $('#header');
    Drupal.behaviors.inntopia.quickCart = $('#quickCart');


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



    Drupal.behaviors.inntopia.isotope = {};
    Drupal.behaviors.inntopia.isotope.filterBtn = $( ".filterItem" );
    Drupal.behaviors.inntopia.isotope.filters = {};
    Drupal.behaviors.inntopia.isotope.grid = undefined;
    Drupal.behaviors.inntopia.isotope.iso = undefined;
    Drupal.behaviors.inntopia.isotope.counter = $('.head-listing-title h5 .counter');
    Drupal.behaviors.inntopia.isotope.init = function( $listingGrid ){

        Drupal.behaviors.inntopia.isotope.grid = $listingGrid;
        Drupal.behaviors.inntopia.isotope.iso = Drupal.behaviors.inntopia.isotope.grid.isotope({
            // options
            itemSelector: '.inntopia-teaser',
            /*transitionDuration: 0,*/
            hiddenStyle: {
                opacity: 0
            },
            visibleStyle: {
                opacity: 1
            },
            getSortData: {
                name: 'h2 a',
                price: '[data-sort-price] parseInt'
            }
        });

        Drupal.behaviors.inntopia.isotope.updateCount();

    };

    Drupal.behaviors.inntopia.isotope.sortBy = function( value ) {

        Drupal.behaviors.inntopia.isotope.grid.isotope({
            // sort by color then number
            sortBy: [ value ]
        });

    };


    Drupal.behaviors.inntopia.isotope.filter = function(){

        Drupal.behaviors.inntopia.isotope.grid.isotope({

            filter: function() {
                if( $.isEmptyObject(Drupal.behaviors.inntopia.isotope.filters) ){
                    // If filters are empty, then display everything (return true)
                    return true;
                }else{
                    // If filters is not empty, use isotope filter function
                    return Drupal.behaviors.inntopia.isotope.checkFilters( $(this), "AND" );
                }
            }
        });

    };

    Drupal.behaviors.inntopia.isotope.updateCount = function(){
        var data =  Drupal.behaviors.inntopia.isotope.iso.data('isotope');
        var count =  data.filteredItems.length;
        Drupal.behaviors.inntopia.isotope.counter.html(count);
    };


    Drupal.behaviors.inntopia.isotope.checkFilters = function( $listItem, $method){

        var index;
        var arrCat = Object.keys(Drupal.behaviors.inntopia.isotope.filters);
        for (index = 0; index < arrCat.length; ++index) {

            var category = arrCat[index];
            var dataValue = $listItem.attr("data-category-"+category);

            // If there is an attribute defined in the list item
            if(dataValue !== undefined){
                var attributeArr = dataValue.split("|");
                // Check if the value exists in the array
                var inArrayResult = $.inArray(Drupal.behaviors.inntopia.isotope.filters[category], attributeArr );

                // AND Method (needs to have all elements matching in order to return true)
                // OR Method (if there is any matching elements, return true)
                if( $method === "AND" ){
                    // AND Method - If it's -1, then it doesn't exist, return false straight away
                    if (inArrayResult === -1 ) { return false; }
                }else{
                    // OR Method - If it's 0 or 1, then it exists, return true
                    if (inArrayResult !== -1 ) { return true; }
                }

            }

        }

        if( $method === "AND" ) {
            // AND Method - return true if nothing has been passed
            return true;
        }else{
            // OR Method - return false if nothing has been passed
            return false;
        }

    };





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


            // Detail Page Carousel
            $('.carousel-large.hasSlider').slick({
                slide: '.image',
                dots: true,
                adaptiveHeight: true
            });


            // Additional Filters
            $('.inntopia-add-filters .filterItem').on( "click", function(event) {

                console.log("clicking filters");

                var $this = $(this);
                var $buttonGroup = $this.parents('.button-group');
                var filterGroup = $buttonGroup.data('filter-group');

                // Checkbox behavior
                if( !$this.hasClass('active') ) {
                    $buttonGroup.find(".filterItem").removeClass('active');
                    $this.addClass('active');
                    // Add the filter element to the main filter list variable
                    Drupal.behaviors.inntopia.isotope.filters[ filterGroup ] = $this.attr('data-filter');
                }else{
                    $buttonGroup.find(".filterItem").removeClass('active');
                    // Remove the filter element from the main filter list variable
                    delete Drupal.behaviors.inntopia.isotope.filters[ filterGroup ];

                }

                Drupal.behaviors.inntopia.isotope.filter();
                Drupal.behaviors.inntopia.isotope.updateCount();

            });


            $(".inntopia-sort #sorting").on("change", function() {
                var value = $(this).val();
                Drupal.behaviors.inntopia.isotope.sortBy( value );
            });


        });


        // Element outside of once (that need to be triggered after page load)


        // Slick thumb Carousel
        $('.carousel-thumb.hasSlider').slick({
            slide: '.image',
            dots: true
        });



    };





}(jQuery, Drupal));
