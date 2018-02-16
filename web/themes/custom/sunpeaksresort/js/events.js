(function ($, Drupal, drupalSettings) {

    'use strict';


    Drupal.behaviors.event = {};
    Drupal.behaviors.event.from;
    Drupal.behaviors.event.to;
    Drupal.behaviors.event.attach = function (context, settings) {

        $('.ajax-calendar', context).once('event').each(function () {

            var from = $( ".from-date" );
            var to = $( ".to-date" );
            var ajaxContainer = $(this);
            var ajaxPath = ajaxContainer.attr("data-ajax-path");
            var hash = window.location.hash;
            var hashValue = hash.substring(1);

            // Update Listing and Filters when filter change
            $(".filter-ajax input").on("change", function() {

                var $this = $(this);

                // get group key
                var filterGroup = $this.attr('data-filter-group');

                // set dates
                Drupal.behaviors.event.from = from.val();
                Drupal.behaviors.event.to = to.val();

                Drupal.behaviors.event.reloadCal(ajaxContainer, ajaxPath);

            });

            // UI Datepicker
            $('.filter-events input').wrap('<div class="wrap-picker"/>');
            $('.wrap-picker').click( function(){
                $(this).find('input').focus();
            });

            var noMonths = 2;
            if(Drupal.behaviors.resizer.isMobile){
                noMonths = 1;
            }
            from.datepicker({
                dateFormat: "dd/mm/yy",
                changeMonth: true,
                changeYear: true,
                showAnim: "fadeIn",
                yearRange: "c:c+1",
                numberOfMonths: noMonths,
                onClose: function( selectedDate ) {
                    $( ".to-date").datepicker( "option", "minDate", selectedDate );
                    $(this).change();
                }
            });
            to.datepicker({
                dateFormat: "dd/mm/yy",
                changeMonth: true,
                changeYear: true,
                showAnim: "fadeIn",
                yearRange: "c:c+1",
                numberOfMonths: noMonths
            });



            // If hash is set, update date fields
            if(hashValue != ''){
                var hashArray = hashValue.split('--');
                $( ".from-date" ).val(hashArray[0]);
                $( ".to-date").val(hashArray[1]);
            }


        });

    };
    Drupal.behaviors.event.reloadCal = function(ajaxContainer, ajaxPath){

        // Hash string
        var hash = Drupal.behaviors.event.from+'--'+Drupal.behaviors.event.to;

        // Abort any possible current ajax call
        Drupal.behaviors.ajaxListLoading.xhr.abort();

        // Load Content
        Drupal.behaviors.ajaxListLoading.displayList(ajaxContainer, ajaxPath, hash);

    };





})(jQuery, Drupal, drupalSettings);
