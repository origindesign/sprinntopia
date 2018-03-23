(function utilScript($, Drupal) {

    'use strict';

    /**
     * Utility Functions
     */
    Drupal.behaviors.utils = {};
    Drupal.behaviors.utils.parseURL = function ( url ){

        var result = {};
        var urlArr = url.split('?');
        var params = [];

        if ( typeof urlArr[1] !== "undefined" ){
            var paramsArr = urlArr[1].split("&");
            var paramsArrLength = paramsArr.length;
            for (var i = 0; i < paramsArrLength; i++) {
                var argArr = paramsArr[i].split("=");
                params[argArr[0]] = ( typeof argArr[1] !== "undefined" ) ? argArr[1] : "" ;
            }
        }
        result.url = urlArr[0];
        result.params = params;

        return result;
    };






}(jQuery, Drupal));