(function ($, Drupal, drupalSettings) {

    'use strict';


    Drupal.behaviors.flickrGallery = {};
    Drupal.behaviors.flickrGallery.apiKey = '0bc8b397caead6d7417c139960447317';
    Drupal.behaviors.flickrGallery.apiSecret = '1afd1ab105afd647';
    Drupal.behaviors.flickrGallery.userID = '148819406@N05';
    Drupal.behaviors.flickrGallery.collectionID = '72157672622576834';
    Drupal.behaviors.flickrGallery.gallery = $("#flickr-gallery");
    Drupal.behaviors.flickrGallery.container = $("#gallery-container");
    Drupal.behaviors.flickrGallery.attach = function (context, settings) {

        $('#flickr-gallery', context).once('flickrGallery').each( function() {

            // Load collections
            if ($('#collections').length > 0) {
                Drupal.behaviors.flickrGallery.createCollections();
            }

            // Load photosets
            if ($('#photoset').length > 0) {
                Drupal.behaviors.flickrGallery.createPhotoset();
            }

        });
    };


    // Create collections
    Drupal.behaviors.flickrGallery.createCollections = function() {

        var collections = $('#collections').hide();

        var flickrSet = Drupal.behaviors.flickrGallery.gallery.flickr({
            api_key         : Drupal.behaviors.flickrGallery.apiKey,
            user_id         : Drupal.behaviors.flickrGallery.userID,
            collection_id     : Drupal.behaviors.flickrGallery.collectionID,
            onItemLoaded    : function ( photoset ){
                //Drupal.behaviors.flickrGallery.addPhotoset(photoset);
            },
            onAllItemLoaded : function (photosets){

                Drupal.behaviors.flickrGallery.addPhotoset(photosets);

                Drupal.behaviors.flickrGallery.container.removeClass('loading');

                collections.fadeIn();
            }
        });

        
    };


    // Add photoset to collections list
    Drupal.behaviors.flickrGallery.addPhotoset = function($photosets) {

        // Loop through all photosets
        $.each($photosets, function($i,$photoset){

            var media = document.createElement('article');
            $(media).attr('class','node node-teaser node-view-grid').attr("rel", $photoset.id).append('<a href="?photoset='+$photoset.id+'"><div class="image" style="background-image:url('+$photoset.thumb+')"></div><h4>'+decodeURI($photoset.title)+'</h4></a>');
            $('#collections').append(media);

        })

    };


    // Create photoset
    Drupal.behaviors.flickrGallery.createPhotoset = function() {

        var gallery = Drupal.behaviors.flickrGallery.container.find('.flickr-gallery').hide();
        var photoset = gallery.attr('data-photoset');

        var flickrSet = gallery.flickr({
            api_key         : Drupal.behaviors.flickrGallery.apiKey,
            user_id         : Drupal.behaviors.flickrGallery.userID,
            collection_id     : '',
            photoset_id     : photoset,
            onItemLoaded    : function ( photo ){
                //Drupal.behaviors.flickrGallery.addMedia(photo, gallery);
            },
            onAllItemLoaded : function (photos){

                // Add photos to gallery div
                Drupal.behaviors.flickrGallery.addMedia(photos, gallery);

                // Add back link and title h2
                gallery
                    .fadeIn()
                    .before('<a href="/explore/photos-and-video" class="back back-to-collections"> Back to Albums</a>')
                    .before('<h2 class="photoset-title"></h2>');

                // Populate title h2 with photoset title
                Drupal.behaviors.flickrGallery.getPhotosetTitle(photoset);

                // Init isotope
                Drupal.behaviors.flickrGallery.initIsotope();

                // Remove Loading
                Drupal.behaviors.flickrGallery.container.removeClass('loading');

            }
        })

    };


    // Get photoset title and prepend to flickr gallery
    Drupal.behaviors.flickrGallery.getPhotosetTitle = function($photoset) {

        var baseUrl = "https://api.flickr.com/services/rest/?&method=flickr.photosets.getInfo&format=json&privacy_filter=5";
        var getUrl = baseUrl + '&api_key=b0fffba79acdd6b8e9af3630e2df7f8a&user_id130803927@N04=&photoset_id='+ $photoset +'&jsoncallback=?';

        // Get data
        $.getJSON( getUrl, function($data) {

            var title = $data.photoset.title._content;
            $('.photoset-title').text(title);

        });

    };
    

    // Add media to flickr gallery
    Drupal.behaviors.flickrGallery.addMedia = function($photos, $gallery) {

        // Loop through all photos
        $.each($photos, function($i,$media){

            // Create photo div
            var media = document.createElement('div');

            $(media)
            // Add attributes
                .attr("id", $media.id)
                .addClass('iso-item')

                // Append image
                .append('<a href="'+$media.src+'"><img src="'+$media.thumb+'" alt="'+$media.title+'" /><div class="caption">'+$media.title+'</div> </a>');

            // Add Items to gallery
            $gallery.append(media);

        });

    };


    // Innit isotope layout
    Drupal.behaviors.flickrGallery.initIsotope = function() {

        var $isoGrid = $('.flickr-gallery');

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

        // Add click event to iso images
        $('.flickr-gallery a').click( function(event){
            event.preventDefault();
            event.stopPropagation();
            var $this = $(this);

            // Put gallery above header
            $('#header').css('z-index','1');

            Drupal.behaviors.gallery.openGallery( $this );

            // Set focus for accessibility
            Drupal.behaviors.gallery.focus = $this;

            // Set parent item for prev/next
            Drupal.behaviors.gallery.parent = $this.parent();

        });

    }





})(jQuery, Drupal, drupalSettings);
