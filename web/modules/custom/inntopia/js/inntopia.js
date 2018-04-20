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


    Drupal.behaviors.inntopia.package = {};
    Drupal.behaviors.inntopia.package.updatePrice = function( $packageId, $productId ) {

        var packageDiv = $('#package-'+$packageId);
        var rowParentDiv = packageDiv.parents('.row');
        var component = packageDiv.find('.component[data-productid="'+ $productId +'"]');

        var quantity = parseInt(component.data('quantity'));
        var originalPrice = parseInt(component.data('componentoriginalprice'));
        var newPrice = parseInt(component.data('componentprice'));

        var divOriginalPrice = component.find(".originalPrice .number");
        var divNewPrice = component.find(".newPrice .number");
        var divComonentPrice = component.find(".componentPrice");

        // Update old del price if exists
        if(divOriginalPrice.length > 0){
            divOriginalPrice.html( quantity * originalPrice );
        }

        // Update new price
        divNewPrice.html( quantity * newPrice );

        // Hide price if equal to 0
        if( quantity * newPrice > 0){
            divComonentPrice.removeClass('hidden');
        }else{
            divComonentPrice.addClass('hidden');
        }

        // Update total package price
        var priceObj = Drupal.behaviors.inntopia.package.calculateTotalPrice($packageId);
        rowParentDiv.find('.sum-price .number').text(priceObj.pricePerNight);


    };


    Drupal.behaviors.inntopia.package.updateDefaultPackagePrice = function(){

        // Loop through all products (ie: rooms)
        $("#pricing .product-data").each(function( index ) {

            if( $(this).find('.detail-price .package-details').length > 0 ){

                // Loop through all package list of the product
                $(this).find('.detail-price .package-details').each(function( index ) {

                    var packageDiv = $(this);
                    var rowParentDiv = packageDiv.parents('.row');

                    // Get the package id
                    var packageId = packageDiv.attr('id').replace('package-','');

                    // Get Price and update it
                    var priceObj = Drupal.behaviors.inntopia.package.calculateTotalPrice(packageId);
                    rowParentDiv.find('.sum-price .number').text(priceObj.pricePerNight);

                });
            }

        });

    };



    Drupal.behaviors.inntopia.package.calculateTotalPrice = function( $packageId ){

        var packageDiv = $('#package-'+$packageId);
        var packageTotalPrice = 0;
        var diffDates = null;
        var result = {
            pricePerNight : null,
            priceTotal : null
        };

        packageDiv.find('.component').each(function( index ) {

            var component = $(this);
            var quantity = parseInt(component.data('quantity'));
            var newPrice = parseInt(component.data('componentprice'));
            var isPerNight = parseInt(component.data('ispernight'));
            var componentTotalPrice =  0;

            if (isPerNight !== 1){
                componentTotalPrice = quantity * newPrice;
            }else{
                var arrivalDate = moment(component.data('arrivaldate'), "YYYY-MM-DD");
                var departureDate = moment(component.data('departuredate'), "YYYY-MM-DD");
                diffDates = departureDate.diff(arrivalDate, 'days');
                componentTotalPrice =  diffDates * newPrice;
            }

            packageTotalPrice += componentTotalPrice;

        });

        result.priceTotal = packageTotalPrice;
        if ( diffDates !== null ){
            result.pricePerNight = parseInt(packageTotalPrice / diffDates);
        }

        return result;

    };





    Drupal.behaviors.inntopia.isotope = {};
    Drupal.behaviors.inntopia.isotope.filterBtn = $( ".filterItem" );
    Drupal.behaviors.inntopia.isotope.filters = {};
    Drupal.behaviors.inntopia.isotope.grid = undefined;
    Drupal.behaviors.inntopia.isotope.iso = undefined;
    Drupal.behaviors.inntopia.isotope.counter = $('.head-listing-title h5 .counter');
    Drupal.behaviors.inntopia.isotope.resultMsg = $('.head-listing-title h5 .copy');
    Drupal.behaviors.inntopia.isotope.init = function( $listingGrid ){

        // Set Carousel Thumb Slider
        $("article.inntopia-teaser").each(function( index ) {
            var allImages = $(this).find('.all-images');
            var carousel = $(this).find('.carousel-thumb');
            carousel.html(allImages.html());
            if ( allImages.hasClass('multipleImages') ) { carousel.addClass('hasSlider'); }
        });

        // Slick thumb Carousel
        $('.carousel-thumb.hasSlider').slick({
            slide: '.image',
            dots: true
        });

        Drupal.behaviors.inntopia.isotope.grid = $listingGrid;
        // Set a small delay to load isotope so we're sure it's loaded and displayed
        setTimeout(function () {
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
        }, 500);

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
        Drupal.behaviors.inntopia.isotope.resultMsg.html('results were found');
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

                // Update map markers if map exists
                if ( $("#map_canvas").length > 0 ){
                    Drupal.behaviors.inntopiaMaps.filterMarkers();
                }


            });


            $(".inntopia-sort #sorting").on("change", function() {
                var value = $(this).val();
                Drupal.behaviors.inntopia.isotope.sortBy( value );
            });


            /**
             * CLicking on toggle to switch view for maps
             */
            $(".inntopia-view a.toggleBtn").on("click", function(event) {

                event.preventDefault();
                event.stopPropagation();

                var selectedItem = $(this).data('select-item');
                var itemToShow =  $('.'+ selectedItem);

                if ( itemToShow.hasClass('hidden') ){
                    itemToShow.siblings('.result-item').addClass('hidden');
                    itemToShow.removeClass('hidden');

                    $(this).siblings('.toggleBtn').removeClass('active');
                    $(this).addClass('active');


                    $('.form-item').removeClass('hidden');
                    $( '.not-related-'+selectedItem ).addClass('hidden');

                    // Relayout Isotope in case some filters have changed
                    Drupal.behaviors.inntopia.isotope.grid.isotope('layout');

                }

            });


            /**
             * Clicking on view package details
             */
            $(".btn-package-detail").on("click", function(event) {

                event.preventDefault();
                event.stopPropagation();

                var relatedPackage = $(this).parents('.row').find('.package-details');
                if( $(this).hasClass('active') ){
                    $(this).removeClass('active');
                    relatedPackage.removeClass('opened');
                }else{
                    $(this).addClass('active');
                    relatedPackage.addClass('opened');
                }

            });



            /**
             * Updating default package Price
             */
            if ( $("#pricing").length > 0 ){
                Drupal.behaviors.inntopia.package.updateDefaultPackagePrice();
            }


            /**
             * Updating package Price on increment Click
             */
            // Input Numeric Value
            $(".component .incrementor").on('click', '.increment' , function(event){

                event.preventDefault();
                event.stopPropagation();

                var parentProduct = $(this).parents('.product-data');
                var component = $(this).parents('.component');
                var parent = $(this).parents('.incrementor');
                var input = parent.siblings('input');

                // Update quantity
                component.attr('data-quantity', input.val());
                component.data('quantity', input.val());

                // Update package price
                Drupal.behaviors.inntopia.package.updatePrice( component.data('packageid')+'-'+parentProduct.data('productid'), component.data('productid') );


            });




        });






    };





}(jQuery, Drupal));
