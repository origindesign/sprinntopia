(function inntopiaMapScript($, Drupal) {

    'use strict';

    /**
     * Map Functions
     */
    Drupal.behaviors.inntopiaMaps = {};
    Drupal.behaviors.inntopiaMaps.gmap = null;
    Drupal.behaviors.inntopiaMaps.infoBox = null;
    Drupal.behaviors.inntopiaMaps.currentDivMarker = null;
    Drupal.behaviors.inntopiaMaps.attach = function (context, settings) {

        $('.result-map', context).once('inntopiaMaps').each(function () {


        });




    };





    Drupal.behaviors.inntopiaMaps.init = function( $mapDiv ){

        var objMarker = {};
        var key = 0;

        $( ".result-list article.inntopia-teaser" ).each(function( index ) {

            var item = $(this);
            if( item.data("supplier-lat") !== '' &&  item.data("supplier-lng") !== '' ){

                var objItem = {
                    marker_id: item.data("supplier-id"),
                    lat: item.data("supplier-lat"),
                    lng: item.data("supplier-lng"),
                    price: item.data("sort-price"),
                    category_lodging: item.data("category-lodging"),
                    category_bedroom: item.data("category-bedroom")
                };

                objMarker[key] = objItem;

                key++;

            }

        });

        var originLatLng = new google.maps.LatLng(objMarker[0].lat, objMarker[0].lng);
        var htmlMarkers = [];
        var mapOptions = {
            zoom: 15,
            center: originLatLng
        };

        Drupal.behaviors.inntopiaMaps.gmap  = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
        Drupal.behaviors.inntopiaMaps.gmap.setOptions({styles: Drupal.behaviors.inntopiaMaps.styles['default']});

        Object.keys(objMarker).forEach(function(key) {
            var markerLatLng = new google.maps.LatLng(objMarker[key].lat, objMarker[key].lng);
            htmlMarkers[key] = new Drupal.behaviors.inntopiaMaps.HTMLMarker(markerLatLng,  Drupal.behaviors.inntopiaMaps.gmap, objMarker[key]);
        });



        // Event that closes the Info Window with a click on the map
        /*
        google.maps.event.addListener(Drupal.behaviors.inntopiaMaps.gmap, 'click', function() {
            console.log('clicking on map');
            if (Drupal.behaviors.inntopiaMaps.infoBox) {
                Drupal.behaviors.inntopiaMaps.infoBox.close();
            }
        });
        */

    };


    Drupal.behaviors.inntopiaMaps.HTMLMarker = function (latlng, map, args){
        this.latlng = latlng;
        this.args = args;
        this.setMap(map);
    };

    Drupal.behaviors.inntopiaMaps.HTMLMarker.prototype = new google.maps.OverlayView();

    Drupal.behaviors.inntopiaMaps.HTMLMarker.prototype.draw = function(){

        var self = this;
        var div = this.div;

        if (!div) {

            div = this.div = document.createElement('div');
            div.className = "htmlMarker";
            div.style.position='absolute';
            div.style.cursor = 'pointer';
            div.innerHTML = '<div class="_m_marker">' +
                    '<div class="_m_wrapper">' +
                        '<div class="_m_peak_1"></div>' +
                        '<div class="_m_container">' +
                            '<div class="_m_table_content">' +
                                '<div class="_m_content">$'+ self.args.price +' CAD</div>' +
                                '<div class="_m_content">' +
                                    '<div style="margin-left:2px"><span class="_m_padder"></span></div>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                        '<div class="_m_peak_2"></div>' +
                    '</div>' +
                '</div>';

            if (typeof(self.args.marker_id) !== 'undefined') {
                div.dataset.marker_id = self.args.marker_id;
            }

            if (typeof(self.args.category_lodging) !== 'undefined') {
                div.dataset.category_lodging = self.args.category_lodging;
            }

            if (typeof(self.args.category_bedroom) !== 'undefined') {
                div.dataset.category_bedroom = self.args.category_bedroom;
            }


            google.maps.event.addDomListener(div, "click", function(event) {
                Drupal.behaviors.inntopiaMaps.clickMarker(self);
                google.maps.event.trigger(self, "click");
                div.style.display = "none";
            });

            var panes = this.getPanes();
            panes.overlayImage.appendChild(div);
        }

        var overlayProjection = this.getProjection();
        var point = overlayProjection.fromLatLngToDivPixel(this.latlng);

        if (point) {
            div.style.left = point.x + 'px';
            div.style.top = point.y + 'px';
        }

    };

    Drupal.behaviors.inntopiaMaps.HTMLMarker.prototype.remove = function() {
        if (this.div) {
            this.div.parentNode.removeChild(this.div);
            this.div = null;
        }
    };

    Drupal.behaviors.inntopiaMaps.HTMLMarker.prototype.getPosition = function() {
        return this.latlng;
    };



    Drupal.behaviors.inntopiaMaps.filterMarkers = function() {

        var currentFilters = Drupal.behaviors.inntopia.isotope.filters;

        // Loop through all markers
        $("#map_canvas .htmlMarker").each(function (index) {

            var marker = $(this);
            var markerHasNonMatchingValue = false;

            // If some filters are set
            if( Object.keys(currentFilters).length > 0 ){

                // Loop through all currently set filters
                Object.keys(currentFilters).forEach(function(key) {

                    // Get the marker category of the current filter
                    var markerCat = marker.data('category_'+key);
                    if( markerCat !== undefined ){
                        var currentValue = currentFilters[key];
                        // If there is (even just one) not a matching value set the variable to true
                        if ( markerCat.toString().indexOf(currentValue.toString()) === -1 ){
                            markerHasNonMatchingValue = true;
                        }
                    }else{
                        // If the variable is not define, it means it doesn't match the filter
                        markerHasNonMatchingValue = true;
                    }

                });

                // Display or not the marker depending on if it has a matching value or not
                if ( markerHasNonMatchingValue ){
                    marker.css({'display': 'none'});
                }else{
                    marker.css({'display': 'block'});
                }

            }else{

                // If no filters are set, display all markers
                marker.css({'display': 'block'});

            }

        });
    };



    Drupal.behaviors.inntopiaMaps.clickMarker = function( marker ){

        // Close info window if there is one already
        if (Drupal.behaviors.inntopiaMaps.infoBox) {
            Drupal.behaviors.inntopiaMaps.infoBox.close();
        }

        //console.log('You clicked the marker: '+marker.args.marker_id);

        // Create new info window
        var content = Drupal.behaviors.inntopiaMaps.getInfoBoxContent(marker.args);
        var boxText = document.createElement("div");
        boxText.className = 'wrapper';
        //boxText.style.cssText = "border: 1px solid black; margin-top: 8px; background: white; padding: 5px;";
        boxText.innerHTML = content;

        var options = {
            content: boxText,
            alignBottom: true,
            disableAutoPan: false,
            maxWidth: 0,
            pixelOffset: new google.maps.Size(-150, 0),
            zIndex: null,
            boxStyle: {
                width: "300px"
            },
            closeBoxMargin: "0",
            closeBoxURL: "/modules/custom/inntopia/misc/images/cross.png",
            infoBoxClearance: new google.maps.Size(1, 1),
            isHidden: false,
            pane: "floatPane",
            enableEventPropagation: false
        };

        // Display the current marker if exists
        if (Drupal.behaviors.inntopiaMaps.currentDivMarker) {
            Drupal.behaviors.inntopiaMaps.currentDivMarker.style.display = "block";
        }

        // Create the new infobox
        Drupal.behaviors.inntopiaMaps.infoBox = new InfoBox(options);
        Drupal.behaviors.inntopiaMaps.infoBox.open( Drupal.behaviors.inntopiaMaps.gmap, marker);

        google.maps.event.addListener(Drupal.behaviors.inntopiaMaps.infoBox, 'closeclick', function(event) {
            Drupal.behaviors.inntopiaMaps.currentDivMarker.style.display = "block";
        });

        google.maps.event.addListener(Drupal.behaviors.inntopiaMaps.infoBox, 'domready', function(event) {
            $('.ib-slider').addClass('loaded');
            $('.ib-slider.hasSlider').slick({
                slide: '.image',
                dots: true
            });
        });

        // Store current marker as the one just clicked
        Drupal.behaviors.inntopiaMaps.currentDivMarker = marker.div;

    };


    Drupal.behaviors.inntopiaMaps.getInfoBoxContent = function (args){

        var relatedTeaser = $('#teaser-'+args.marker_id);
        var title = relatedTeaser.find('.info h2').html();
        var images = relatedTeaser.find('.all-images').html();
        var review = relatedTeaser.find('.inn-review').html();
        var address = relatedTeaser.find('.inn-address').html();
        var cta = relatedTeaser.find('.cta').html();
        var sliderClass = ( relatedTeaser.find('.all-images').hasClass('multipleImages') ) ? 'hasSlider' : '';

        var content = '<div class="ib-container inntopia-teaser">' +
                '<div class="ib-slider '+ sliderClass +'">'+ images +'</div>' +
                '<div class="ib-content">' +
                    '<h2 class="ib-title">' + title + '</h2>' +
                    '<div class="ib-copy">' +
                        '<div class="ib-copy-left">' +
                            '<div class="inn-review">' + review + '</div>' +
                            '<div class="inn-address">' + address + '</div>' +
                        '</div>' +
                        '<div class="ib-copy-right cta">' + cta + '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';

        return content;

    };




    Drupal.behaviors.inntopiaMaps.styles = {
        default:[
            {
                "featureType": "poi",
                "elementType": "labels.text",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },{
                "featureType": "poi.business",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },{
                "featureType": "road",
                "elementType": "labels.icon",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },{
                "featureType": "transit",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            }
        ]
    };











}(jQuery, Drupal));