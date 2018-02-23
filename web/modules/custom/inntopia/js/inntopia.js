(function inntopiaScript($, Drupal) {

    'use strict';


    Drupal.behaviors.inntopia = {};

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




        });
    };


}(jQuery, Drupal));
