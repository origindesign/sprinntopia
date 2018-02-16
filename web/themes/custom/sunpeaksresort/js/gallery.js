(function ($, Drupal, drupalSettings) {

    'use strict';


    Drupal.behaviors.gallery = {};
    Drupal.behaviors.gallery.container = $('#gallery-overlay');
    Drupal.behaviors.gallery.focus = '';
    Drupal.behaviors.gallery.parent = '';
    Drupal.behaviors.gallery.attach = function (context, settings) {

        $('#page', context).once('gallery').each(function () {

            // Click on overlay area
            $('#gallery-overlay, #gallery-overlay .inner').on("click", function(event) {
                if (event.target !== this) return;
                Drupal.behaviors.gallery.closeGallery();
            });

            // Click on overlay close
            Drupal.behaviors.gallery.container.find('.close').click( function(event){
                event.preventDefault();
                event.stopPropagation();
                Drupal.behaviors.gallery.closeGallery();
            });

            // Next/prev arrows
            Drupal.behaviors.gallery.container.find('.overlay-arrow').click( function(event){

                event.preventDefault();
                event.stopPropagation();
                var $this = $(this);

                if($this.hasClass('next')){
					var $parent = Drupal.behaviors.gallery.parent.next('.iso-item');
					if(!$parent.length){
						$parent = Drupal.behaviors.gallery.parent.siblings('.iso-item').first();
					}

                }else{
                    var $parent =  Drupal.behaviors.gallery.parent.prev('.iso-item');
					if(!$parent.length){
						$parent = Drupal.behaviors.gallery.parent.siblings('.iso-item').last();
					}
                }
				$parent.find('a').click();

            });


        });

    };


    // Function that opens the overlay
    Drupal.behaviors.gallery.openGallery = function ( href ){

        // Parse player and images information
        var $result = Drupal.behaviors.gallery.getEmbedCode( href );
        var $widgetDiv = $('#gallery-overlay');
        var $placeholder = $widgetDiv.find(".placeholder");

        // Empty Placeholder and add embedCode
        $placeholder.html($result).hide();

        // Remove Scroll on Body
        $("html").addClass("overlayed");

        // Open overlay
        $widgetDiv.fadeIn( function(){
            $(this).focus().find('.placeholder').fadeIn()
        });

    };


    // Function that closes the overlay
    Drupal.behaviors.gallery.closeGallery = function (){

        // Close overlay and empty placeholder
        $('#gallery-overlay').fadeOut().find(".placeholder").html('');

        // Remove Scroll on Body
        $("html").removeClass("overlayed");

        // Reset header z-index
        $('#header').attr('style','');

        // Set focus back to clicked element
        Drupal.behaviors.gallery.focus.focus();

    };



    // Function that parse a URL and return required element as an array
    Drupal.behaviors.gallery.getEmbedCode = function ( $a ){

        var result = "";
        var uri = $a.uri();

        if ( uri.suffix() == "jpg" || uri.suffix() == "png" ){

            result = $("<img />",{
                "src":uri,
                "typeof": "foaf:Image",
                "alt": "Image Gallery"
            });

        }else{

            var domain = uri.domain();
            var directID = uri.path().replace("/","");
            var paramID = uri.query().replace("v=","");
            var iframeURL = '';

            switch ( domain ){

                case "youtube.com": iframeURL = "https://www.youtube.com/embed/"+paramID;  break;
                case "youtu.be": iframeURL = "https://www.youtube.com/embed/"+directID;  break;
                case "vimeo.com": iframeURL = "https://player.vimeo.com/video/"+directID;  break;
            }

            result =  '<div class="video-embed-field-responsive-video"><iframe src="'+iframeURL+'" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>';

        }

        return result;

    };




})(jQuery, Drupal, drupalSettings);
