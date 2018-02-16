

jQuery.fn.accessibleNavigation = function (settings) {

    var settings = jQuery.extend({
        orientation: 'vertical',
        menuHoverClass: 'is-visible'
    }, settings);


    var nav = jQuery(this);


    // All top level links
    // -------------------------------
    var firstLevelLinks = nav.find('> li > a');

    firstLevelLinks.each( function() {

        var $firstLevelLink = jQuery(this);

        // Setup hover/focus for all links to hide subs
        $firstLevelLink.hover( function(){
            hideAllSubs();
        }).focus( function(){
            hideAllSubs();
        });


        // Add attributes to all parent links
        $firstLevelLink.attr('aria-haspopup', 'true').attr('aria-expanded', 'false').attr('aria-controls', 'nav-item-' + ($firstLevelLink.parents('li').index() + 1))

        // Show second level list on focus
        .focus( function(){
            showSub($firstLevelLink);
        })

        // Capture key presses
        .keydown(function(e) {

            // Vertical navigation
            if(settings.orientation == 'vertical') {
                switch (e.keyCode) {

                    // Up
                    case 38:
                        // If is first list item, set focus to last item
                        if ($firstLevelLink.parent('li').prev('li').length == 0) {
                            $firstLevelLink.parents('ul').find('> li').last().find('a').first().focus();
                        }
                        // Else set focus to prev item
                        else {
                            $firstLevelLink.parent('li').prev('li').find('a').first().focus();
                        }
                        e.preventDefault();
                        break;

                    // Down
                    case 40:
                        // If is last list item, set focus to first item
                        if ($firstLevelLink.parent('li').next('li').length == 0) {
                            $firstLevelLink.parents('ul').find('> li').first().find('a').first().focus();
                        }
                        // Else set focus to next item
                        else {
                            $firstLevelLink.parent('li').next('li').find('a').first().focus();
                        }
                        e.preventDefault();
                        break;

                    // Right, space, enter
                    case 39:
                    case 32:
                    case 13:
                        // If has sub
                        if($firstLevelLink.parent('li').find('ul').length > 0){
                            // Set focus to first item in child list
                            $firstLevelLink.parent('li').find('ul').find('a').first().focus();
                            e.preventDefault();
                        }
                        break;

                    // Escape
                    case 27:
                        $firstLevelLink.parents('.inner').siblings('.close').focus();

                }
            }

        })

    });


    // All second level links
    // -------------------------------
    var secondLevelLinks = nav.find('> li ul a');

    secondLevelLinks.each( function() {

        jQuery(this).attr('tabindex','-1')

        // Capture key presses
        .keydown(function(e) {

            // Vertical navigation
            if(settings.orientation == 'vertical') {
                switch (e.keyCode) {

                    // Up
                    case 38:
                        // If is first list item, set focus to last item
                        if (jQuery(this).parent('li').prev('li').length == 0) {
                            jQuery(this).closest('ul').find('> li').last().find('a').first().focus();
                        }
                        // Else set focus to prev item
                        else {
                            jQuery(this).parent('li').prev('li').find('a').first().focus();
                        }
                        e.preventDefault();
                        break;

                    // Down
                    case 40:
                        // If is last list item, set focus to first item
                        if (jQuery(this).parent('li').next('li').length == 0) {
                            jQuery(this).closest('ul').find('> li').first().find('a').first().focus();
                        }
                        // Else set focus to next item
                        else {
                            jQuery(this).parent('li').next('li').find('a').first().focus();
                        }
                        e.preventDefault();
                        break;

                    // Left, escape
                    case 37:
                    case 27:
                        // Set focus to parent first level item
                        jQuery(this).closest('ul').parents('li').removeClass('is-active').find('a').first().focus();
                        jQuery(this).parents('.inner').removeClass('third-open');
                        e.preventDefault();
                        break;

                }
            }

        })

    });


    // Add attributes to all child lists
    // -------------------------------
    var secondLevelLists = nav.find('ul');
    secondLevelLists.each( function(){
        jQuery(this).attr('aria-hidden','true').attr('aria-expanded','false').attr('aria-labelledby',jQuery(this).parents('li').find('a').first().attr('aria-controls'));
    });


    // Hide menu if click or focus occurs outside of navigation
    // -------------------------------
    nav.find('a').last().keydown(function(e){
        // Tab
        if(e.keyCode == 9) {
            hideAllSubs();
        }
    });
    jQuery(document).click(function(){ hideAllSubs(); });
        

    // Show sub menu
    // -------------------------------
    var showSub = function(jQueryitem) {

        // Hide all subs
        hideAllSubs();

        // Add attributes to parent link
        jQueryitem.attr('aria-expanded','true');

        // Add attributes to child list and add child items to tabindex
        jQueryitem.parents('li').find('ul').attr('aria-hidden','false').attr('aria-expanded','true').addClass(settings.menuHoverClass).find('a').attr('tabindex','0');

    };


    // Hide sub menus
    // -------------------------------
    var hideAllSubs = function() {

        nav.find('ul').attr('aria-hidden','true').attr('aria-expanded','false').removeClass(settings.menuHoverClass).find('a').attr('aria-expanded','false').attr('tabindex','-1');

    };


};

