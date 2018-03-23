(function inntopiaWidget($, Drupal) {

    'use strict';

    /**
     * ****************************************************************************************
     * WIDGET BOOKING FUNCTIONS ***********************************************************************
     * ****************************************************************************************
     */


    Drupal.behaviors.inntopiaWidget = {};
    Drupal.behaviors.inntopiaWidget.attach = function (context, settings) {

        // Global call only triggered once
        $('body').once('inntopiaWidget').each(function () {


            $('body').find("a.inntopia-widget").each( function(event){
                Drupal.behaviors.inntopiaWidget.transformBtn( $(this) );
            });


        });

    };




    Drupal.behaviors.inntopiaWidget.transformBtn = function ( button ){

        var url = button.attr('href');
        var urlObj = Drupal.behaviors.utils.parseURL(url);
        var uniqueId = Math.random().toString(36).substring(7);
        var copyLink = button.text();

        var form = $("<form/>", { action: urlObj.url, method: 'GET', id: 'form-'+uniqueId, class: "form-inntopia-widget" });
        var date = moment().add(3,'days');

        // Add date picker for start date into form
        form.append(
            $("<div>", {
                class: "datepicker-wrap"
            }).append(
                $("<input>",
                    { type:'text',
                        name:'startDate',
                        class:'datepicker',
                        value: date.format("YYYY-MM-DD")
                    }
                )
            )
        );


        // Add params as hidden form field
        for (var key in urlObj.params){
            if (urlObj.params.hasOwnProperty(key)) {
                if (key !== "startDate"){
                    form.append(
                        $("<input>",
                            { type:'hidden',
                                name: key,
                                value: urlObj.params[key] }
                        )
                    );
                }

            }
        }

        // Add date picker for start date into form
        form.append(
            $("<button>",
                { type:'submit',
                    form: 'form-'+uniqueId,
                    value: copyLink,
                    text: copyLink,
                    class:'form-submit' }
            )
        );

        button.hide(0).after(form);


        $('.form-inntopia-widget .datepicker').datepicker({
            dateFormat: "yy-mm-dd",
            changeMonth: true,
            changeYear: true,
            showAnim: "fadeIn",
            yearRange: "c:c+2"
        });

    };






}(jQuery, Drupal));