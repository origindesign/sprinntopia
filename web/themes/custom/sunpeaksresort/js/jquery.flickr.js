/*
 * jQuery flickR Photoset v0.0.1
 * Contributing Author: Sebastien Lamy
 * Date: 2014-02-18
 */


;(function ($, doc, win) {
    'use strict';

    $.fn.flickr = function (options) {


        // ---------------------------------------------------------------------
        // SUPPORT FOR MULTIPLE ELEMENTS
        // ---------------------------------------------------------------------
        if (this.length > 1) {
            this.each(function () { $(this).flickrphotoset(options); });
            return this;
        }
        


        // ---------------------------------------------------------------------
        // DEFAULT SETTINGS
        // ---------------------------------------------------------------------
        var settings = $.extend({
            api_key             : null,
            user_id             : null,
			collection_id         : null,
            photoset_id         : null,
            size_photo          : "medium",
            onItemLoaded        : function () {},
            onAllItemLoaded      : function () {}
        }, options);



        // ---------------------------------------------------------------------
        // PRIVATE VARIABLES
        // ---------------------------------------------------------------------
        var _obj = this;
        var _flickr;
        var _nbPhotos = 0;
        var _markerPhoto = 0;
        var _result = [];
        var _flickrSizeArr = {
            square          : { id: 0, ext: "_s" },
            large_square    : { id: 1, ext: "_q" },
            thumbnail       : { id: 2, ext: "_t" },
            small           : { id: 3, ext: "_m" },
            small_320       : { id: 4, ext: "_n" },
            medium          : { id: 5, ext: null },
            medium_640      : { id: 6, ext: "_z" },
            medium_800      : { id: 7, ext: "_c" },
            large           : { id: 8, ext: "_l" },
            original        : { id: 9, ext: "_o" }
        };



        // ---------------------------------------------------------------------
        // PRIVATE METHODS
        // ---------------------------------------------------------------------



        // get photo sets from collection
        var _getCollection = function() {
            
            var baseUrl = "https://api.flickr.com/services/rest/?&method=flickr.collections.getTree&format=json";
            var getUrl = baseUrl + '&api_key=' + settings.api_key + '&user_id=' + settings.user_id + '&collection_id=' + settings.collection_id + '&jsoncallback=?';
            
            // Loop all the photosets
            $.getJSON( getUrl, function(data) {
                _browseCollectionJson(data); 
            });

        };
		
		
		// Browse Json file
        var _browseCollectionJson = function($data){

            // Loop through all collections
            $.each($data.collections.collection, function($i, $item){
				
				// Get the number of collections so we could create a call back when last is added
            	_nbPhotos = $item.set.length;
				
				$.each($item.set, function($i,$item){
					
					_getPhotoset($i,$item);
					
				});
				
            });
            
        };
		
		
		// Get photoset info
		var _getPhotoset = function($index,$photoset){
			
			var baseUrl = "https://api.flickr.com/services/rest/?&method=flickr.photosets.getInfo&format=json";
            var getUrl = baseUrl + '&api_key=' + settings.api_key + '&user_id=' + settings.user_id + '&photoset_id=' + $photoset.id + '&jsoncallback=?';
            
            // Get size of photo
            $.getJSON( getUrl, function($data) {
				
				var item = {};
				
				// Set info
				item.id = $photoset.id;
				item.title = encodeURI ($photoset.title);
				item.thumb = 'http://farm'+$data.photoset.farm+'.static.flickr.com/'+$data.photoset.server+'/'+$data.photoset.primary+'_'+$data.photoset.secret+'.jpg';
				
				// Increment marker
				_markerPhoto++;
								
				 // Add item to main array
				_result[$index] = item;
				
				// Call back for each item added
				//settings.onItemLoaded.call( _obj, item );
	
				// Call Back when all items are added to ruls array
				if (_markerPhoto === _nbPhotos){
					settings.onAllItemLoaded.call( _obj, _result );
                    console.log(_result);
				}
				
			});

		
		};
		
		
		// get Photos from set
        var _getSetPhotos = function(photoset_id) {
            
            var baseUrl = "https://api.flickr.com/services/rest/?&method=flickr.photosets.getPhotos&format=json&per_page=100&privacy_filter=3";
            var getUrl = baseUrl + '&api_key=' + settings.api_key + '&user_id=' + settings.user_id + '&photoset_id=' + photoset_id + '&jsoncallback=?';
            
            // Loop all the photos of the set
            $.getJSON( getUrl, function(data) {
                _browseSetJson(data);
            });
        };
        
        
        // Browse Json file
        var _browseSetJson = function($data){
            
            // Get the number of photos so we could create a call back when last is added
            _nbPhotos = $data.photoset.photo.length;
            
            // Loop through all photos
            $.each($data.photoset.photo, function($i,$item){
                _getPhoto($i,$item);
            });
            
        };
        
        
        // Get Image info for wih and height
        var _getPhoto = function($index, $photo){
            
            var baseUrl = "https://api.flickr.com/services/rest/?&method=flickr.photos.getSizes&format=json";
            var getUrl = baseUrl + '&api_key=' + settings.api_key + '&photo_id=' + $photo.id + '&jsoncallback=?';
            var indexSize = _flickrSizeArr[settings.size_photo].id;

            // Get size of photo
            $.getJSON( getUrl, function($data) {

                var item = {};
                
                // Define attr of photo for thumb
                var maxSizeId = $data.sizes.size.length - 1;
                var fitIdSize = ( indexSize > maxSizeId ) ? maxSizeId : indexSize;
                var fitDataThumb = $data.sizes.size[fitIdSize];
 
                item.id = $photo.id;
                item.title = encodeURI ($photo.title);
                item.width = parseInt(fitDataThumb.width);
                item.height = parseInt(fitDataThumb.height);
                item.thumb = fitDataThumb.source;
                item.src = $data.sizes.size[maxSizeId].source;
                
                // Increment marker
                _markerPhoto++;
                                
                 // Add item to main array
                _result[$index] = item;
                
                // Call back for each item added
                //settings.onItemLoaded.call( _obj, item );

                // Call Back when all items are added to ruls array
                if (_markerPhoto === _nbPhotos){
                    settings.onAllItemLoaded.call( _obj, _result );
                }
                
            });
    
        };
 

        // ---------------------------------------------------------------------
        // PUBLIC METHODS
        // ---------------------------------------------------------------------

        // Init
        this.initialize = function() {
          	
			// get collection if ID is set
			if(settings.collection_id != ''){

				_getCollection();
				
			} else{
			
				// get photos from flickr set
				_getSetPhotos(settings.photoset_id);
			
			}
            
            // Return element
            return this;
        };
		



        // ---------------------------------------------------------------------
        // RETURN 
        // ---------------------------------------------------------------------
        return this.initialize();



    };

})(jQuery, document, window);