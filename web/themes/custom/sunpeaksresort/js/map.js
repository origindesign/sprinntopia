(function ($, Drupal, drupalSettings) {

    'use strict';


    Drupal.behaviors.interactiveMap = {};
    Drupal.behaviors.interactiveMap.isMapDisplayed = false;
    Drupal.behaviors.interactiveMap.icons = { path : '/themes/custom/sunpeaksresort/images/markers/', size : new google.maps.Size(34, 42), origin: new google.maps.Point(0,0), anchor: new google.maps.Point(17, 21) };

    Drupal.behaviors.interactiveMap.attach  = function (context, settings) {

        $('#interactive-map').once('interactiveMap').each(function () {

            // Initialize map
            var goomapAll = $('#interactive-map').goomap({
                urlJson : '/geodata',
                infoWindow : [ 'title','address','link' ],
                center: new google.maps.LatLng(50.884462, -119.885900),
                zoom: 15,
                icons : Drupal.behaviors.interactiveMap.icons,

                onMapDisplayed: function(){
                    Drupal.behaviors.interactiveMap.isMapDisplayed = true;
                    var categories = this.getAllCategories();
                    Drupal.behaviors.interactiveMap.loopCategories(categories, this);
                }
            });

            // When a category checkbox changes: show/hide markers
            $('#filters').on( "change", "input.filter" , function(event) {
                // Get checked filters
                var filters = Drupal.behaviors.interactiveMap.getCheckedFilters();
                // Display // Hide markers based on filters
                goomapAll.displayMarkers(filters);
            });

            // Closing Filters on Mobile
            $('#categories').on( "click", "a.filter" , function(event) {
                event.preventDefault();
                Drupal.behaviors.interactiveMap.mobileFilterClose();
            });

            // Click categories toggle on mobile
            $('#map-container .toggle-categories').click( function(){
                Drupal.behaviors.interactiveMap.mobileFilterOpen();
            });

        });

    };

    Drupal.behaviors.interactiveMap.loopCategories = function ($categories, $map){
        // Store Length of category array
        var categorieLength = $categories.length;
        // If category array is not empty
        if( categorieLength > 0){
            // Loop trhough all categories
            for (var i = 0; i < categorieLength; i++){
                this.addInputToForm($categories[i].category_id, $categories[i].category_name);
            }
        }
        // Get default value of filters and display them
        var filters = Drupal.behaviors.interactiveMap.getCheckedFilters();
        $map.displayMarkers(filters);
    };

    Drupal.behaviors.interactiveMap.addInputToForm = function ($category_id, $category_name){
        var result = '<div class="mapFilter"><label class="'+$category_id+'"><input class="filter" type="checkbox" value="'+$category_id+'" checked="checked" /> '+$category_name+'</label></div>';
        $(result).appendTo( "#filters" );
    };

    Drupal.behaviors.interactiveMap.getCheckedFilters = function (){
        var checkedFilter = [];
        // Loop through all filter checkboxes o see which one are checked
        $('#filters input.filter').each(function( index ) {
            var $this = $(this);
            if ($this.is(':checked')) {
                checkedFilter.push( $this.val() );
            }
        });
        return (checkedFilter);
    };

    Drupal.behaviors.interactiveMap.mobileFilterOpen = function(){
        $("#map-container #categories").fadeIn();
    };

    Drupal.behaviors.interactiveMap.mobileFilterClose = function(){
        $("#map-container #categories").fadeOut();
    };



})(jQuery, Drupal, drupalSettings);
