(function ($, Drupal, drupalSettings) {

    'use strict';


    /* --------------------------------------------------- */
    /* GLOBAL
    /* --------------------------------------------------- */

    Drupal.behaviors.global = {
        attach: function(context, settings) {

            $('body').once('global').each(function () {

                // Objectfit polyfill
                objectFitImages();

                // Nano scroll on overlay
                $('.nano').nanoScroller();

                // To top
                var topButton = $('a.to-top');
                var $window = $(window);

                $window.scroll( function(){
                    if($window.scrollTop() > $window.height()){
                        topButton.addClass('show');
                    }else{
                        topButton.removeClass('show');
                    }
                });

                topButton.on('click', function(event){
                    event.preventDefault();
                    event.stopPropagation();
                    $('html, body').animate({ scrollTop: 0 });
                });

                // Match Heights
                $('.field--name-field-blogs .image, .field--name-field-blogs .copy').matchHeight();
                $('.field--name-field-featured-package, .field--name-field-featured-event').matchHeight();
                $('.node-view-grid.iso-item').matchHeight({byRow: false});
                $('#contact-message-race-centre-booking-form-form .day .field--widget-options-buttons legend').matchHeight();

                // Add span to table captions
                $('table caption').wrapInner('<span></span>');

                // Add scroll indicator to tables
                $('.template.table table').each( function(){
                   if($(this).width() > $('#content').width()){
                       $(this).before('<span class="scroll-indicator">Scroll</span>');
                   }
                });

                // UI Datepicker
                $( ".section-newsletter .date-picker" ).datepicker({
                    changeMonth: true,
                    changeYear: true,
                    yearRange: "c-100:c"
                });
                $( "#start-date" ).datepicker({
                    minDate: 0
                }).datepicker("setDate", new Date());

                $('.wrap-picker').click( function(){
                    $(this).find('input').focus();
                });

                // Currency Convertor
                var usd = $('.usd-rate').text();

                if(usd < 1){

                    $('.field--name-field-price').each( function(){

                        if($(this).text().indexOf('$') > -1){
                            $(this).wrapInner('<span class="currency-convert" />');
                        }

                    });

                    $('.currency-convert').each( function(){

                        var $this = $(this);
                        var cdn = $this.text().replace('$','');
                        var conversion = parseInt(usd*cdn);
                        $this.attr('data-rate','US$'+conversion+'');

                        $this.on('click', function(){
                            $this.toggleClass('hover');
                            $('.currency-convert').not($this).removeClass('hover');
                        })

                    });

                    $(window).scroll( function(){
                        $('.currency-convert').removeClass('hover');
                    });

                    document.addEventListener('touchmove', function(e) {
                        $('.currency-convert').removeClass('hover');
                    }, true);

                }

                // Input Numeric Value
                $('.incrementor').on('click', '.increment' , function(event){

                    var parent = $(this).parents('.incrementor');
                    var input = parent.siblings('input');
                    var currentValue = parseInt(input.val());
                    var minValue = ( input.attr('data-min') ) ? parseInt(input.data('min')) : 0;
                    var maxValue = ( input.attr('data-max') ) ? parseInt(input.data('max')) : 1000;

                    if( $(this).hasClass('minus') && ( currentValue > minValue) ){
                        input.val( currentValue - 1 );
                        input.attr('value', currentValue - 1 );
                    }

                    if( $(this).hasClass('plus')  && ( currentValue < maxValue) ){
                        input.val( currentValue + 1 );
                        input.attr('value', currentValue + 1 );
                    }

                });



                // Social sharing
                $('a.share-facebook').on('click',function(event){
                    event.preventDefault();
                    event.stopPropagation();
                    window.open("https://www.facebook.com/sharer/sharer.php?u="+location.href, "pop", "width=400, height=400");
                });
                $('a.share-twitter').on('click',function(event){
                    event.preventDefault();
                    event.stopPropagation();
                    window.open("https://twitter.com/share?url="+location.href, "pop", "width=600, height=400");
                });


                // Accordion
                $('.accordions').on('click', '.accordion-header' ,function(event){

                    event.preventDefault();
                    event.stopPropagation();

                    var $accordion = $(this).parents(".accordion");
                    var $content = $(this).siblings('.accordion-content');


                    // Open this accordion
                    if ( !$accordion.hasClass('active') ){

                        $('.accordions .accordion').each( function(){
                            $(this).removeClass('active');
                            $(this).find('.accordion-content').removeClass('open');
                            //$(this).find('.accordion-content').slideUp();
                        });

                        $accordion.addClass('active');
                        $content.addClass('open');
                        //$content.slideDown();

                    }else{

                        $('.accordions .accordion').each( function(){
                            $(this).removeClass('active');
                            $(this).find('.accordion-content').removeClass('open');
                            //$(this).find('.accordion-content').slideUp();
                        });

                    }



                });


            });

        }
    };



    /* --------------------------------------------------- */
    /* TABLES
     /* --------------------------------------------------- */

    Drupal.behaviors.responsiveTable = {
        attach: function(context, settings) {

            $('.node table td', context).once('responsiveTable').each(function (index) {

                // If 2 column table, do not add responsive table class
                var nbTd = $(this).siblings().addBack().length;
                if (nbTd > 2) {
                    $(this).parents('table').addClass("responsive-table");
                }

                // Add class for empty cells
                if($(this).text().length <= 1){
                    $(this).addClass('empty-cell');
                }

                // Get label of td and add as html data attr
                var indexTd = $(this).index();
                var textTh = $.trim($(this).parents('table').find("thead tr th").eq(indexTd).text());
                if (textTh === "")$(this).addClass("empty-row");
                $(this).attr("data-label", textTh);

            });

        }

    };



    /* --------------------------------------------------- */
    /* FORMS
    /* --------------------------------------------------- */

    Drupal.behaviors.formstone = {
        attach: function(context, settings) {

            $('#main').once('formstone').each(function () {

                // Radio and checkboxes
                $("input[type=checkbox], input[type=radio]").checkbox();


                // Select Dropdown
                if(Drupal.behaviors.resizer.isMobile || Drupal.behaviors.resizer.isTablet){
                    $("select").dropdown({
                        mobile: true
                    });
                }else{
                    $("select").dropdown();
                }



                // File Input
                var inputs = document.querySelectorAll( '.inputfile' );
                Array.prototype.forEach.call( inputs, function( input )
                {
                    var label	 = input.nextElementSibling,
                        labelVal = label.innerHTML;

                    input.addEventListener( 'change', function( e )
                    {
                        var fileName = '';
                        if( this.files && this.files.length > 1 )
                            fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{count}', this.files.length );
                        else
                            fileName = e.target.value.split( '\\' ).pop();

                        if( fileName )
                            label.querySelector( 'span' ).innerHTML = fileName;
                        else
                            label.innerHTML = labelVal;
                    });
                });

            });

        }
    };



    /* --------------------------------------------------- */
    /* SLICK SLIDESHOWS
    /* --------------------------------------------------- */

    Drupal.behaviors.slicks = {
        attach: function(context, settings) {

            // Fragment slideshow
            $('#page').once('slicks').each(function () {

                $('.node-type-fragment .slideshow img').each( function(){
                    if($(this).attr('alt') != ''){
                        $(this).after('<div class="caption">'+$(this).attr('alt')+'</div>');
                    }
                });
                $('.slick.slideshow').slick({
                    dots: true,
                    adaptiveHeight: true
                });

                // Home activities
                $('.slideshow-center').slick({
                    centerMode: true,
                    centerPadding: '0',
                    mobileFirst: true,
                    responsive: [
                        { breakpoint: 1599,
                            settings: { centerPadding: '250px'}
                        },
                        { breakpoint: 599,
                            settings: { centerPadding: '50px'}
                        }
                    ]
                });

                // Home Blogs
                $('.slideshow-split').slick({
                    dots: true,
                    arrows: false,
                    adaptiveHeight: true
                })

            });

        }
    };



    /* --------------------------------------------------- */
    /* ALERT BAR
    /* --------------------------------------------------- */

    Drupal.behaviors.alert = {};
    Drupal.behaviors.alert.xhr;
    Drupal.behaviors.alert.attach  = function (context, settings) {

        $('#page', context).once('alert').each(function () {

            // call click action for existing alert bar
            Drupal.behaviors.alert.checkAlert();

            var $alert = $('#alertbar');

            if(!$alert.length){

                Drupal.behaviors.alert.xhr = $.ajax({
                    type: 'GET',
                    url: '/node/471', // Resort settings page
                    data: { response_type: 'ajax' },
                    success: function(result) {

                        // Get values from result
                        var showAlert = $(result).find('.field--name-field-show-alert .field__item').text();
                        var alertText = $(result).find('.field--name-field-alert-text .field__item').text();
                        var alertLink = $(result).find('.field--name-field-alert-link').text();
                        var cookie = alertText.toLowerCase().replace(/&amp;/g, "").replace(/ /g, "-");

                        // Add bar
                        if(showAlert == '1'){

                            $('#header > .inner').before('<div id="alertbar" data-cookie="'+cookie+'"><div class="container"><div class="table"><div class="inner"></div> </div><a class="close" href="#"></a></div></div>');
                            if(alertLink != ''){
                                $('#alertbar .inner').html('<a class="link" href="'+alertLink+'">'+alertText+'</a>');
                            }else{
                                $('#alertbar .inner').html(alertText);
                            }

                            // Add body class
                            $('body').addClass('alert-on');

                            // Add click
                            Drupal.behaviors.alert.checkAlert();

                        }

                    }
                });

            }

        });


    };

    Drupal.behaviors.alert.checkAlert = function( ){

        $('#alertbar').each(function () {

            var $this = $(this);
            var cookieName = $this.attr("data-cookie");

            // If there is no cookie (eval null), then display the bar
            if ( $.cookie(cookieName) === null ){
                // Show the alert bar
                $this.addClass('visible');
                $('body').addClass('alert-on');
            }else{
                $('body').removeClass('alert-on');
            }

            $this.find("a.close").on("click", function(event) {
                event.preventDefault();
                $.cookie(cookieName, 'visited', {path:'/'} );
                $this.removeClass('visible').fadeOut( function(){
                    $('body').removeClass('alert-on');
                });
            });


        });

    };



    /* --------------------------------------------------- */
    /* IMPERIAL/METRIC CONVERTER
    /* --------------------------------------------------- */

    Drupal.behaviors.converter = {};
    Drupal.behaviors.converter.kmToMi = function($value){
        return Math.round($value / 1.609344);
    };
    Drupal.behaviors.converter.miToKm = function($value){
        return Math.round($value * 1.609344);
    };
    Drupal.behaviors.converter.cmToIn = function($value){
        return ($value / 2.54).toFixed(1);
    };
    Drupal.behaviors.converter.inToCm = function($value){
        return Math.round($value * 2.54);
    };
    Drupal.behaviors.converter.mToFt = function($value){
        $value = $value.replace(',','');
        return Math.round( $value * 3.2808399).toLocaleString();
    };
    Drupal.behaviors.converter.ftToM = function($value){
        $value = $value.replace(',','');
        return Math.round($value / 3.2808399).toLocaleString();
    };
    Drupal.behaviors.converter.cToF = function($value){
        return Math.round(($value * 1.8) + 32);
    };
    Drupal.behaviors.converter.fToC = function($value){
        return Math.round(($value - 32) * .5556);
    };
    Drupal.behaviors.converter.attach  = function (context, settings) {

        $('#snow-conditions').once('converter').each( function(){
            // Add in values after
            $('#snow-conditions .value_cm').each( function(){
                $(this).after('<span class="value_in" style="display:none">'+Drupal.behaviors.converter.cmToIn($(this).text())+'</span>');
            });
        });

        $('.converter').once('converter').each(function () {

            $(this).click( function(event){
                event.preventDefault();
                event.stopPropagation();
                var $this = $(this);

                if($this.attr('data-unit') == 'metric') {

                    $('.converter').attr('data-unit','imperial');
                    // cm to in
                    $('.value_cm').each( function(){
                        $(this).hide().next().show();
                    });
                    $('.unit_cm').text('in');
                    // m to ft
                    $('.value_m').each( function(){
                        $(this).text(Drupal.behaviors.converter.mToFt($(this).text()));
                    });
                    $('.unit_m').text('ft');
                    // C to F
                    $('.value_deg').each( function(){
                        $(this).text(Drupal.behaviors.converter.cToF($(this).text()));
                    });
                    $('.unit_deg').text('F');
                    // km to mi
                    $('.value_kph').each( function(){
                        $(this).text(Drupal.behaviors.converter.kmToMi($(this).text()));
                    });
                    $('.unit_kph').text('mph');

                }else{

                    $('.converter').attr('data-unit','metric');
                    // in to cm
                    $('.value_in').each( function(){
                        $(this).hide().prev().show();
                    });
                    $('.unit_cm').text('cm');
                    // ft to m
                    $('.value_m').each( function(){
                        $(this).text(Drupal.behaviors.converter.ftToM($(this).text()));
                    });
                    $('.unit_m').text('m');
                    // F to C
                    $('.value_deg').each( function(){
                        $(this).text(Drupal.behaviors.converter.fToC($(this).text()));
                    });
                    $('.unit_deg').text('C');
                    // mi to km
                    $('.value_kph').each( function(){
                        $(this).text(Drupal.behaviors.converter.miToKm($(this).text()));
                    });
                    $('.unit_kph').text('kph');

                }

            })

        });

    };



    /* --------------------------------------------------- */
    /* ISOTOPE LISTING
    /* --------------------------------------------------- */

    Drupal.behaviors.isotope = {};
    Drupal.behaviors.isotope.filters = {};
    Drupal.behaviors.isotope.concatValues = function( obj ){
        var value = '';
        for ( var prop in obj ) {
            value += obj[ prop ];
        }
        return value;
    };
    Drupal.behaviors.isotope.attach  = function (context, settings) {

        $('.isoGrid').once('isotope').each(function () {

            var $isoGrid = $('.isoGrid');

            // Adding gutter element in the iso grid
            $isoGrid.append('<div class="iso-gutter"></div>');

            // Setting up Isotope listing
            var $grid = $isoGrid.isotope({
                masonry: {
                    columnWidth: '.iso-item',
                    gutter: '.iso-gutter'
                },
                itemSelector: '.iso-item',
                percentPosition: true
            });

            var $iso = $grid.data('isotope');

            $grid.imagesLoaded($grid, function(){
                $iso.layout();
            });

            Drupal.behaviors.isotope.filter = function( obj ){
                var $this = obj;

                // get group key
                var filterGroup = $this.attr('data-filter-group');

                // set filter for group
                Drupal.behaviors.isotope.filters[ filterGroup ] = $this.val();

                // combine filters
                var filterValue = Drupal.behaviors.isotope.concatValues( Drupal.behaviors.isotope.filters );

                // Update Isotope
                $grid.isotope({ filter: filterValue });

                // No results
                if ( !$grid.data('isotope').filteredItems.length ) {
                    $('.isoGrid .no-results').fadeIn();
                }else{
                    $('.isoGrid .no-results').hide();
                }
            };

            // Filter list
            $(".filter-isotope .dropdown").on("change", function() {

                Drupal.behaviors.isotope.filter($(this));

            }).each( function(){

                // If value pre-populated, Filter list
                var $this = $(this);
                if($this.val() != ''){
                    Drupal.behaviors.isotope.filter($this);
                }

            });


            $(".filter-isotope .clear-filters").on("click", function(event) {

                event.preventDefault();
                var $select = $(".filter-isotope .dropdown");
                $select.val('');
                $select.dropdown("update");
                Drupal.behaviors.isotope.filters = {};
                $grid.isotope({ filter: "" });

            });


        });


    };



    /* --------------------------------------------------- */
    /* GOOGLE MAP ON ADDRESS FIELD
    /* --------------------------------------------------- */

    Drupal.behaviors.singleMap = {
        attach: function(context, settings) {

            $('.field--name-field-location', context).once('singleMap').each(function () {

                // MAPS - FUNCTIONS
                var isMapDisplayed = false;

                var mapContainer = $(".field--name-field-location");

                if(mapContainer.length !== 0){
                    var latlng = mapContainer.find(".value_geodata").text();
                    var singleDataMap = mapContainer.find("#single-data-map");
                    var arrLatLng = latlng.split(",");
                    //var node = '[{"latitude" : "'+arrLatLng[0]+'","longitude" : "'+arrLatLng[1]+'","marker" : "marker_1"}]';
                    var node = '[{"latitude" : "'+Number(arrLatLng[0])+'","longitude" : "'+Number(arrLatLng[1])+'","type" : "accommodation"}]';
                    var goomapSingle = singleDataMap.goomap({
                        dataJson: node,
                        zoom: 14,
                        center: new google.maps.LatLng(parseFloat(arrLatLng[1]), parseFloat(arrLatLng[0])),
                        mapOptions : { mapTypeControl: false, streetViewControl: false, scrollwheel: false },
                        icons : { path : '/themes/custom/sunpeaksresort/images/markers/', size : new google.maps.Size(34, 42), origin: new google.maps.Point(0,0), anchor: new google.maps.Point(17, 21) },
                        onMapDisplayed: function(){
                            isMapDisplayed = true;
                        }
                    });
                }


            });

        }
    };



    /* --------------------------------------------------- */
    /* VIDEO GALLERY
    /* --------------------------------------------------- */

    Drupal.behaviors.videoGallery = {
        attach: function(context, settings) {

            $('#page', context).once('videoGallery').each(function () {

                //Action when click on thumbs to open overlay
                var $btnGallery = $('.node-type-video a');
                $btnGallery.on("click", function (event) {

                    event.preventDefault();
                    event.stopPropagation();

                    Drupal.behaviors.gallery.openGallery($(this));

                    // Set focus for accessibility
                    Drupal.behaviors.gallery.focus = $(this);

                    // Set parent item for prev/next
                    Drupal.behaviors.gallery.parent = $(this).parents('.node-type-video');

                });

            });
        }
    };



    /* --------------------------------------------------- */
    /* PARALLAX
    /* --------------------------------------------------- */

    Drupal.behaviors.parallax = {};
    Drupal.behaviors.parallax.controllerMagic = new ScrollMagic.Controller();
    Drupal.behaviors.parallax.attach  = function (context, settings) {

        // Wait for images
        $('#hero').imagesLoaded( function(){
            $('#hero').addClass('loaded');
        });

        if ( Drupal.behaviors.resizer.isDesktop ) {

            $('.parallax').once('parallax').each(function () {

                var $this = $('.parallax');
                var $background = $this.find('.background');
                var $foreground = $this.find('.foreground');
                var $text = $this.find('.text');
                //var $button = $this.find('.cta');
                //var $buttonOverlay = $this.find('.cta-overlay');
                var $initialFront = parseInt($foreground.css('bottom'));
                var $initialText = parseInt($text.css('top'));
                //var $initialButton = parseInt($button.css('top'));
                if($('#hero').hasClass('winter')){
                    var textMovement = 70;
                }else{
                    var textMovement = 20;
                }

                // Setup parallax animation
                var tween = new TimelineMax()
                    .add([
                        TweenMax.fromTo($background, 0.5, {top: "0%"}, {top: "80%", ease: Linear.easeNone }),
                        TweenMax.fromTo($foreground, 0.5, {bottom: $initialFront}, {bottom: ($initialFront-50)+'%', ease: Linear.easeNone}),
                        TweenMax.fromTo($text, 0.5, {top: $initialText}, {top: ($initialText+textMovement)+'%', ease: Linear.easeNone}),
                        TweenMax.fromTo($text, 0.3, {opacity:1}, { opacity:0, ease: Power3.easeOut})
                        //TweenMax.to($button, 0.5, {top: "150%", ease: Linear.easeNone}),
                        //TweenMax.fromTo($button, 0.3, {opacity:1}, { opacity:0, ease: Power3.easeOut}),
                        //TweenMax.to($buttonOverlay, 0.5, {top: "150%", ease: Linear.easeNone})
                    ]);


                var scene = new ScrollMagic.Scene({
                    duration: ( $(window).height() )
                }).setTween(tween).addTo(Drupal.behaviors.parallax.controllerMagic);

                /*$buttonOverlay.hover( function(){
                    $button.find('.btn-solid').addClass('hover');
                }, function(){
                    $button.find('.btn-solid').removeClass('hover');
                });*/


            });

        }else{
            $('.parallax').each(function () {
                $('.background, .foreground, #hero .text').attr('style', '');
                $(window).scroll( function(){
                    $('.background, .foreground, #hero .text').attr('style', '');
                });
            });
        }
    };

    Drupal.behaviors.headerParallax = {};
    Drupal.behaviors.headerParallax.controllerMagic = new ScrollMagic.Controller();
    Drupal.behaviors.headerParallax.attach  = function (context, settings) {

        if ( !Drupal.behaviors.resizer.isTablet && !Drupal.behaviors.resizer.isMobile ) {

            $('#page-title').once('headerParallax').each(function () {

                var $this = $('#page-title h1');

                var tween = new TimelineMax()
                    .add([
                        TweenMax.fromTo($this, 0.5, {top: 0, opacity:1}, {top: "130px", opacity:0, ease: Linear.easeNone })
                    ]);


                var scene = new ScrollMagic.Scene({
                    duration: ( $('#page-title').height() + 100 )
                }).setTween(tween).addTo(Drupal.behaviors.headerParallax.controllerMagic);


            });

        }
    };



    /* --------------------------------------------------- */
    /* HOME
    /* --------------------------------------------------- */

    Drupal.behaviors.home = {
        attach: function(context, settings) {

            $('.path-frontpage').once('home').each(function () {

                // Personas
                $('.personas-list li').hover( function(){
                    var $index = $(this).index();
                    $('.personas .image img').eq($index+1).fadeIn().siblings().fadeOut();
                });

                // Tracking
                $(this).find('.field--name-field-featured-package a').addClass('home-promo-first');
                $(this).find('.field--name-field-featured-event a').addClass('home-promo-second');
                $(this).find('#booking a').addClass('home-book-now');
                $(this).find('#newsletter .btn-solid').addClass('home-sign-up');

            });

        }
    };



    /* --------------------------------------------------- */
    /* NEWSLETTER
    /* --------------------------------------------------- */

    Drupal.behaviors.newsletter = {
        attach: function(context, settings) {

            $('#newsletter-signup').once('newsletter').each(function () {

                // Newsletter form
                $('#newsletter-signup form').validate();

            });

            $('#media-signup').once('newsletter').each(function () {

                // Media form
                $('#media-signup form').validate();

            });

        }
    };



    /* --------------------------------------------------- */
    /* CONTEST FORM
     /* --------------------------------------------------- */

    Drupal.behaviors.contestForm = {
        attach: function(context, settings) {

            $('#contact-message-12-days-form').once('contestForm').each(function () {

                $(this).prepend('<div class="description">Limit of ONE entry per person. Multiple entries will be disqualified.</div>');

                var $desc = $('#edit-field-newsletter-sign-up-wrapper #edit-field-newsletter-sign-up--wrapper--description').remove();
                $('#edit-field-newsletter-sign-up').before($desc);

            });

        }
    };



    /* --------------------------------------------------- */
    /* SITEMAP
    /* --------------------------------------------------- */

    Drupal.behaviors.sitemap = {
        attach: function(context, settings) {

            $('.path-sitemap').once('sitemap').each(function () {

                $('.sitemap-box-menu').each( function(){
                   $(this).find('li.expanded > a').attr('href','');
                });

            });

        }
    };




    /* --------------------------------------------------- */
    /* WEATHER WIDGET AJAX LOAD
    /* --------------------------------------------------- */

    Drupal.behaviors.weatherWidget = {};
    Drupal.behaviors.weatherWidget.xhr;
    Drupal.behaviors.weatherWidget.attach = function (context, settings) {

        $('#page', context).once('weatherWidget').each(function () {

            var $icon = $('.utility .weather');
            var $overlay = $('#weather-overlay');

            Drupal.behaviors.weatherWidget.xhr = $.ajax({
                type: 'GET',
                url: '/ski-ride/weather-conditions-cams/weather-snow-report', // Weather page
                data: { response_type: 'ajax' },
                success: function(result) {

                    // Get values from result
                    var topTemp = $(result).find('.top-temp').text();
                    var midMountainTemp = $(result).find('.mid-mountain-temp').text();
                    var valleyTemp = $(result).find('.valley-temp').text();
                    var icon = $(result).find('.larger_icon > span').attr('class');
                    var todayDescription = $(result).find('.today-description').text();
                    var snow24 = $(result).find('.snow-24').text();
                    var snow7 = $(result).find('.snow-7').text();
                    var liftsOpen = $(result).find('.lifts-open').text();
                    var trailsOpen = $(result).find('.trails-open').text();

                    // Add values to widget
                    if($icon.hasClass('winter')){
                        $icon.find('.temp').text(midMountainTemp);
                    }else{
                        $icon.find('.temp').text(valleyTemp);
                    }
                    $icon.find('.icon').attr('class',icon);
                    $icon.css('visibility','visible');
                    $overlay.find('.top-temp').text(topTemp);
                    $overlay.find('.mid-mountain-temp').text(midMountainTemp);
                    $overlay.find('.valley-temp').text(valleyTemp);
                    $overlay.find('.current-condition .icon').addClass(icon);
                    $overlay.find('.today-description').text(todayDescription);
                    $overlay.find('.snow-24').text(snow24)
                        .after('<span class="value_in" style="display:none">'+Drupal.behaviors.converter.cmToIn(snow24)+'</span>');
                    $overlay.find('.snow-7').text(snow7)
                        .after('<span class="value_in" style="display:none">'+Drupal.behaviors.converter.cmToIn(snow7)+'</span>');
                    $overlay.find('.lifts-open').text(liftsOpen);
                    $overlay.find('.trails-open').text(trailsOpen);
                }
            });


        });

    };




})(jQuery, Drupal, drupalSettings);