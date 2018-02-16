(function ($, Drupal, drupalSettings) {


    'use strict';

    

    // Windows Resize Behavior
    Drupal.behaviors.resizer = {}
    Drupal.behaviors.resizer.resizeTimer;
    Drupal.behaviors.resizer.isMobile = false;
    Drupal.behaviors.resizer.isTablet = false;
    Drupal.behaviors.resizer.isDesktop = true;
    Drupal.behaviors.resizer.isExtended = false;
    Drupal.behaviors.resizer.deviceHasChanged = false;
    Drupal.behaviors.resizer.attach = function(context, settings) {
        $(window).resize(function() {
            clearTimeout(Drupal.behaviors.resizer.resizeTimer);
            Drupal.behaviors.resizer.resizeTimer = setTimeout(function() { Drupal.attachBehaviors(); }, 100);
        });
    };

    

    // Check if mobile device based on header class display on media queries
    Drupal.behaviors.checkViewport = {
        attach: function(context, settings) {

            // Store previous status of device
            var $previousIsMobile =  Drupal.behaviors.resizer.isMobile;
            var $previousIsTablet =  Drupal.behaviors.resizer.isTablet;

            // If Mobile
            if ( $("#nav-toggle").css("display") === 'block') {
                Drupal.behaviors.resizer.isMobile = true;
                Drupal.behaviors.resizer.isTablet = false;
                Drupal.behaviors.resizer.isDesktop = false;
                Drupal.behaviors.resizer.isExtended = false;
                $('body').addClass('is-mobile').removeClass('is-tablet');
            }else{
                // If Tablet
                Drupal.behaviors.resizer.isMobile = false;
                if ( $('#main-nav').css('top') === '49px') {
                    Drupal.behaviors.resizer.isTablet = true;
                    Drupal.behaviors.resizer.isDesktop = false;
                    Drupal.behaviors.resizer.isExtended = false;
                    $('body').addClass('is-tablet').removeClass('is-mobile');
                }else{
                    Drupal.behaviors.resizer.isTablet = false;
                    $('body').removeClass('is-tablet').removeClass('is-mobile');
                    if ( $('#main-nav').css('top') == '0px' ){
                        // If Desktop
                        Drupal.behaviors.resizer.isDesktop = true;
                        Drupal.behaviors.resizer.isExtended = false;
                    }else{
                        // If Extended
                        Drupal.behaviors.resizer.isDesktop = false;
                        Drupal.behaviors.resizer.isExtended = true;
                    }
                }
            }

            // Compare with previous status to see if it has changed
            if( Drupal.behaviors.resizer.isMobile !== $previousIsMobile || Drupal.behaviors.resizer.isTablet !== $previousIsTablet){
                Drupal.behaviors.resizer.deviceHasChanged = true;
                Drupal.behaviors.navigation.resetNavigation();
            }else{
                Drupal.behaviors.resizer.deviceHasChanged = false;
            }


            /*
            console.log("Status Mobile :"+  Drupal.behaviors.resizer.isMobile);
            console.log("Status Tablet :"+  Drupal.behaviors.resizer.isTablet);
            console.log("Status Desktop :"+  Drupal.behaviors.resizer.isDesktop);
            console.log("Status Extended :"+  Drupal.behaviors.resizer.isExtended);
            */



        }
    };




})(jQuery, Drupal, drupalSettings);
