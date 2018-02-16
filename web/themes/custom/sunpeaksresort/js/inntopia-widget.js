
(function ($, Drupal, drupalSettings) {


    'use strict';


    Drupal.behaviors.inntopia = {};
    Drupal.behaviors.inntopia.salesId = 593730; 
    //replace '000000' with your salesId
    Drupal.behaviors.inntopia.widgetType = 1; 
    //Select one of the following: 1=lodging search,  2=activity search, 3=hotel/location lodging search
    Drupal.behaviors.inntopia.productSuperCategoryId = null; 
    //required for widgetType: 2. Refer to http://www.inntopia.com/inntopia/templates/reports/productcategories_RAW.xml
    Drupal.behaviors.inntopia.supplierId = 593730; 
    //required for widgetType: 3
    Drupal.behaviors.inntopia.productCategoryId = 2; 
    //optional for widgetType: 2. to http://www.inntopia.com/inntopia/templates/reports/productcategories_RAW.xml
    Drupal.behaviors.inntopia.domain = 'www.inntopia.travel'; 
    //update with your custom domain, if available
    Drupal.behaviors.inntopia.language = 'en-US'; 
    //only en-US currently supported for this widget


    Drupal.behaviors.inntopia.attach  = function (context, settings) {

        $('#lodging-widget', context).once('inntopia').each(function () {

            $("#checkAvailability").click(function () {

                $("#formError").html('');
                var thisArrivalDate = $("#arrivalDate").val();
                var thisDepartureDate = $("#departureDate").val();
                var thisAdultCount = $("#adultCount").val();
                var thisChildCount = $("#childCount").val();
                var thisChildAgeArray = null;

                var hasError = false;
                if (thisArrivalDate == '') {
                    $("#formError").html('<div class="text-error">Please enter arrival date</div>');
                    hasError = true;
                }
                if (thisDepartureDate == '') {
                    $("#formError").html('<div class="text-error">Please enter departure date</div>');
                    hasError = true;
                }
                if (isNaN(thisAdultCount)) {
                    $("#formError").html('<div class="text-error">Please enter number of adults</div>');
                    hasError = true;
                }
                if (isNaN(thisChildCount)) {
                    $("#formError").html('<div class="text-error">Please enter number of children</div>');
                    hasError = true;
                }
                if ((thisAdultCount === '0') && (thisAdultCount === '0')) {
                    $("#formError").html('<div class="text-error"> Please enter the number of guests</div>');
                    hasError = true;

                }
                if (thisAdultCount === '') {
                    $("#formError").html('<div class="text-error">Please enter number of adults</div>');
                    hasError = true;
                }


                var today = new Date();
                var arrivalDateDT = new Date(thisArrivalDate);

                var departureDateDT = new Date(thisDepartureDate);


                if (arrivalDateDT < today) {
                    $("#formError").html('<div class="text-error">Arrival date must be today or later</div>');
                    hasError = true;
                }

                if (arrivalDateDT >= departureDateDT) {
                    $("#formError").html('<div class="text-error">Departure date must be after arrival date</div>');
                    hasError = true;
                }

                if (hasError === false) {
                    // setDefaultsCookie(thisArrivalDate, thisDepartureDate, thisAdultCount, thisChildCount, thisChildAgeArray, null);
                    // $.blockUI({ message: $("#waitMessage") });
                    var inntopiaSearchURL;
                    if (Drupal.behaviors.inntopia.widgetType==1) {
                        inntopiaSearchURL = 'http://' + Drupal.behaviors.inntopia.domain + '/ecomm/shop/lodging/' + Drupal.behaviors.inntopia.salesId + '/' + Drupal.behaviors.inntopia.language + '/?arrivalDate=' + thisArrivalDate + '&departureDate=' + thisDepartureDate + '&adultcount=' + thisAdultCount + '&childCount=' + thisChildCount;
                    }
                    else if (Drupal.behaviors.inntopia.widgetType==3) {
                        inntopiaSearchURL = 'http://' + Drupal.behaviors.inntopia.domain + '/ecomm/listings/supplierdetail/' + Drupal.behaviors.inntopia.salesId + '/' + Drupal.behaviors.inntopia.language + '/?supplierid=' + Drupal.behaviors.inntopia.supplierId + '&arrivalDate=' + thisArrivalDate + '&departureDate=' + thisDepartureDate + '&adultcount=' + thisAdultCount + '&childCount=' + thisChildCount + '#Availability';
                    }

                    //window.location = inntopiaSearchURL;
                    window.open(inntopiaSearchURL);

                }
            });

            var arrivalDateElement = $("#arrivalDate");
            var departureDateElement = $("#departureDate");
            var startDateElement = $("#startDate");

            var noMonths = 2;
            if(Drupal.behaviors.resizer.isMobile){
                noMonths = 1;
            }

            arrivalDateElement.datepicker({
                defaultDate: "+1w",
                minDate: 0,
                changeMonth: true,
                numberOfMonths: noMonths,
                regional: Drupal.behaviors.inntopia.language,
                onClose: function (selectedDate) {
                    departureDateElement.datepicker("option", "minDate", selectedDate);
                }
            }).datepicker("setDate", 1);

            departureDateElement.datepicker({
                defaultDate: "+1w",
                changeMonth: true,
                numberOfMonths: noMonths,
                regional: Drupal.behaviors.inntopia.language,
                minDate: 0,
                onClose: function (selectedDate) {
                    arrivalDateElement.datepicker("option", "maxDate", selectedDate);
                }
            }).datepicker( "setDate", 2);


            startDateElement.datepicker({
                defaultDate: "+1w",
                changeMonth: true,
                numberOfMonths: 1,
                regional: Drupal.behaviors.inntopia.language,
                minDate: 0
            });

        });
    
    };


    
})(jQuery, Drupal, drupalSettings);
