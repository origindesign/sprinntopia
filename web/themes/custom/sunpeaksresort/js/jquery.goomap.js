/*
 * jQuery goomap v0.0.1
 * Contributing Author: Sebastien Lamy
 * Date: 2014-02-18
 */


;(function ($, doc, win) {
    'use strict';

    $.fn.goomap = function (options) {




        // ---------------------------------------------------------------------
        // SUPPORT FOR MULTIPLE ELEMENTS
        // ---------------------------------------------------------------------
        if (this.length > 1) {
            this.each(function () { $(this).goomap(options); });
            return this;
        }


        // ---------------------------------------------------------------------
        // MAIN MAP OBJECT
        // ---------------------------------------------------------------------
        var $googlemaps = google.maps;



        // ---------------------------------------------------------------------
        // DEFAULT SETTINGS
        // ---------------------------------------------------------------------
        var settings = $.extend({
            urlJson : null, // json path to where geo data are stored in case of external URL use
            dataJson : '{"nodes" : [{"node" : {"latitude" : "41.85","longitude" : "-87.65"}}]}', // default value for 1 marker display
            autoDisplay : true, // if false, map display need to be called manually via the function displayMap()
            icons : null, // Default is using google defaul icon
            onDataLoaded : function () {}, // Callback function when geo data are loaded
            onMapDisplayed : function () {}, // Callback function when entire map is displayed
            mapOptions : {
                center: new $googlemaps.LatLng(41.85, -87.65),
                zoom: 4,
                scrollwheel: false,
                panControl: false,
                mapTypeControl: true,
                scaleControl: false,
                streetViewControl: false,
                zoomControl: true,
                zoomControlOptions: { style: $googlemaps.ZoomControlStyle.SMALL, position: $googlemaps.ControlPosition.RIGHT_TOP},
                mapTypeId: $googlemaps.MapTypeId.ROADMAP
            }, // As per google map options
            zoom : null,
            center: null,
            styles : [], // As per google map style
            infoWindow : null, // default fields to be displayed
            maxWidthWindow : 300, // default width for infow window
            keyForImg : ["image", "images", "photos", "img", "media"], // default image key for displaying image
            splitter: ";" // Define a delimiter for multiple categories
        }, options);





        // ---------------------------------------------------------------------
        // PRIVATE VARIABLES
        // ---------------------------------------------------------------------
        var _obj = this;
        var _map;
        var _idContainer;
        var _allData;
        var _allMarkers = [];
        var _allInfoWindow = [];
        var _allCategories = [];
        var _tempCategories = [];
        var _imageKeyArr = settings.keyForImg;
        var _bounds = new google.maps.LatLngBounds();




        // ---------------------------------------------------------------------
        // PRIVATE METHODS
        // ---------------------------------------------------------------------



        // get Geo Data from given URL (json only)
        var _getGeoData = function() {
            if( settings.urlJson !== null ){
                $.getJSON(settings.urlJson, function(data) {
                    _browseJson (data);
                });
            }else{
                var data = $.parseJSON(settings.dataJson);
                _browseJson (data);
            }
        };


        // Browse Json file (either it's a string or a URL)
        var _browseJson = function($data){
            // Store All Data
            _allData = $data;
            // Call Back when data are loaded
            settings.onDataLoaded.call( _obj );
            // Display Map if autoDIsplay is true
            if (settings.autoDisplay === true){
                _obj.displayMap();
            }
        };



        // Loop Data to get Geo info
        var _loopNodes = function() {

            $.each(_allData, function( index, value ) {
                var geoData = _allData[index];
                // Add Marker to the Map
                _addMarker(geoData);
                // Store Category
                _storeCategories(geoData);
            });
            // Call Back when entire map is displayed (all nodes have been explored)
            settings.onMapDisplayed.call( _obj );
        };



        // Add a marker to the map
        var _addMarker = function($item) {
            // Get position
            var position = new $googlemaps.LatLng($item.latitude,$item.longitude);
            // Get icon if passed on settings
            var icon = null;
            if( settings.icons !== null ){
                // Get type as marker
                var itemMarker = $item.type.replace(/ /g, "").replace(/&amp;/g, "").toLowerCase();
                // setup Icon
                icon = { url: settings.icons.path+'marker_'+itemMarker+".svg", size: settings.icons.size, origin: settings.icons.origin, anchor: settings.icons.anchor };
            }
            // Create marker
            var marker = new $googlemaps.Marker({
                position: position,
                map: _map,
                icon: icon
            });

            // Extend map bounds with marker position
            _bounds.extend(position);

            // Loop through all item properties and create properties to marker
            for (var propertyName in $item) {
                eval("marker."+propertyName+"='"+$item[propertyName]+"'");
            }

            // Add marker to main array of markers
            _allMarkers.push(marker);

            // Define Info Window
            if ( settings.infoWindow !== null ){
                var infowindow = new $googlemaps.InfoWindow({
                    content: _templateInfoWindow(marker),
                    maxWidth: settings.maxWidthWindow
                });

                // Add listener for markers to open info window
                $googlemaps.event.addListener(marker, 'click', function() {
                    _closeAllInfoWindows();
                    _allInfoWindow.push(infowindow);
                    infowindow.open(_map,marker);
                });
            }
        };


        // Store Categories
        var _storeCategories = function($item) {
            // Split array of category in case there are multiple
            if ($item.type !== undefined){
                var arrId = $item.type.replace(/ /g, "").replace(/&amp;/g, "").toLowerCase();
                var arrName = $item.type;
                // Loop through all categories of the node
                var tempId = arrId;
                // If category doesn't exist yet
                if ( $.inArray(tempId, _tempCategories) === -1 ){
                    var category = {
                        category_id: tempId,
                        category_name: arrName
                    };
                    // Store Categories in array (temp is used for better performance when checking if already exists)
                    _tempCategories.push(tempId);
                    _allCategories.push(category);
                }
            }
        };


        // Close all info window
        var _closeAllInfoWindows = function(){
            for (var i=0;i<_allInfoWindow.length;i++) {
                _allInfoWindow[i].close();
            }
        };



        // Generate Info Window
        var _templateInfoWindow = function($marker){
            var tpl = '<div id="info-window content">';
            for (var i =0;i<settings.infoWindow.length;i++) {
                if( $marker[settings.infoWindow[i]] !== "" && $marker[settings.infoWindow[i]] !== "null" ){
                    // Add img tag if match one of the possible images key define in private
                    var field = ($.inArray(settings.infoWindow[i], _imageKeyArr) === -1 ) ? $marker[settings.infoWindow[i]] : '<img src="'+$marker[settings.infoWindow[i]]+'" alt="" />';
                    // Define the key of the object as the class for styling purpose
                    var classes = settings.infoWindow[i];
                    tpl += '<div class="'+classes+'">'+field+'</div>';
                }
            }
            tpl += '</div>';

            return tpl;
        };


        // Utility function to get intersection between 2 arrays
        function _intersect_safe(a, b){
            var ai=0, bi=0;
            var result = new Array();
            while( ai < a.length && bi < b.length ){
                if      (a[ai] < b[bi] ){ ai++; }
                else if (a[ai] > b[bi] ){ bi++; }
                else /* they're equal */ {
                    result.push(a[ai]);
                    ai++;
                    bi++;
                }
            }
            return result;
        }






        // ---------------------------------------------------------------------
        // PUBLIC METHODS
        // ---------------------------------------------------------------------

        // Init
        this.initialize = function() {
            // Store ID of current container
            _idContainer = this.attr("id");
            // get geo data
            _getGeoData();
            // Return element
            return this;
        };



        // Load the Map
        this.displayMap = function() {
            // Create Map
            _map = new $googlemaps.Map(document.getElementById(_idContainer), settings.mapOptions);
            // Apply style if some are passed on setings
            _map.setOptions({styles: settings.styles});
            // Set Center and Zoom if passed as Param
            if (settings.zoom !== null){ _map.setZoom(settings.zoom); }
            if (settings.center !== null){ _map.setCenter(settings.center); }
            // Loop all markers to add them
            _loopNodes();

            // Event listener to set max zoom
            var zoomChangeBoundsListener =
                $googlemaps.event.addListener(_map, 'bounds_changed', function(event) {
                    $googlemaps.event.removeListener(zoomChangeBoundsListener);
                    _map.setZoom( Math.min( 17, _map.getZoom() ) );
                });

            // Fit Bounds
            _map.fitBounds(_bounds);

        };

        // Return all the categories of the geo data
        this.getAllCategories = function(){
            return _allCategories;
        };


        // Show Markers based on passed value
        this.displayMarkers = function(filters) {
            // Hide All info Window
            _closeAllInfoWindows();
            // Get length of markers
            var nbMarkers = _allMarkers.length;
            // Loop through all markers
            for (var i=0; i<nbMarkers; i++){
                // Get category id into a table and sort it
                var type = _allMarkers[i].type.replace(/ /g, "").replace(/&amp;/g, "").toLowerCase();
                // Check if type is in array
                if ( $.inArray(type, filters) !== -1 ){
                    // If there is at least 1 common value, show the marker
                    _allMarkers[i].setVisible(true);
                }else{
                    // If there is no common value hide the marker
                    _allMarkers[i].setVisible(false);
                }
            }
        };


        // Hide all markers
        this.hideAllmarkers = function(){
            var nbMarkers = _allMarkers.length;
            for (var i=0; i<nbMarkers; i++){
                _allMarkers[i].setVisible(false);
            }
        };



        // ---------------------------------------------------------------------
        // RETURN 
        // ---------------------------------------------------------------------
        return this.initialize();



    };

})(jQuery, document, window);