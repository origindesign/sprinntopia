(function ($, Drupal, drupalSettings) {


    'use strict';


    // Declare Navigation Object and variables on it
    Drupal.behaviors.navigation = {};
    Drupal.behaviors.navigation.mainMenu = $('#main-nav');
    Drupal.behaviors.navigation.mainLevelFocus = '';


    /* --------------------------------------------------- */
    /* MAIN NAV
    /* --------------------------------------------------- */
	
    Drupal.behaviors.navigation.attach = function (context, settings) {

        // Global call only triggered once
        Drupal.behaviors.navigation.mainMenu.once('navigation').each(function () {

            // Accessible Navigation plugin
            $('#block-explore-menu > ul.menu, #block-ski-ride-menu > ul.menu, #block-bike-hike-menu > ul.menu, #block-golf-menu > ul.menu, #block-events-menu > ul.menu, #block-places-to-stay-menu > ul.menu').accessibleNavigation();

            // Defining Variables
            var $body = $('body');
            var $primaryLinks = $('#main-nav ul.menu a').attr('href','#');
            var $secondaryLinks = $('.sub-menu ul.menu li > .title');
            var $close = $('#menu-overlay .close');
            var $mobileToggle = $('#nav-toggle a');


            // Click mobile toggle
            $mobileToggle.on( 'click', function(event){
                var $clickedItem = $(this);

                if(!$clickedItem.hasClass('close')){
                    $('html').addClass('overlayed');
                    $clickedItem.addClass('close');
                    Drupal.behaviors.navigation.mainMenu.fadeIn();
                    Drupal.behaviors.navigation.closeUtilityOverlays();
                }else{
                    Drupal.behaviors.navigation.closeMobileMenu();
                    $('html').removeClass('overlayed');
                }

                event.preventDefault();
                event.stopPropagation();
            });


            // Click primary menu links
            $primaryLinks.on( "click", function(event) {
                var $clickedItem = $(this);
                Drupal.behaviors.navigation.mainLevelFocus = $clickedItem;

                if(Drupal.behaviors.resizer.isMobile){
                    $body.addClass('mobile-second-open');
                    Drupal.behaviors.navigation.openSubMenu($clickedItem);
                }else{
                    if($clickedItem.hasClass('is-active')){
                        Drupal.behaviors.navigation.closeMenuOverlay();
                        $('html').removeClass('overlayed');
                    }else{
                        if(Drupal.behaviors.resizer.isMobile || Drupal.behaviors.resizer.isTablet){
                            $('html').addClass('overlayed');
                        }
                        Drupal.behaviors.navigation.openSubMenu($clickedItem);
                    }
                }

                // Nano scroll on overlay
                $('.nano').nanoScroller();

                event.preventDefault();
                event.stopPropagation();
            });


            // Click secondary menu links
            $secondaryLinks.on( "click", function(event) {
                var $clickedItem = $(this);
                var $ul = $clickedItem.next('ul');

                $clickedItem.parents('.sub-menu .inner').addClass('third-open');
                $clickedItem.parent().addClass('is-active').siblings().removeClass('is-active');

                event.preventDefault();
                event.stopPropagation();
            }).keydown(function(e){
                var $clickedItem = $(this);

                // Right, space, enter, tab && not shift
                if( e.keyCode == 39 || e.keyCode == 32 || e.keyCode == 13 || (e.keyCode == 9 && !e.shiftKey )){
                    e.preventDefault();
                    $clickedItem.parents('.sub-menu .inner').addClass('third-open');
                    $clickedItem.parent().addClass('is-active').siblings().removeClass('is-active');
                    $clickedItem.parent('li').find('ul').find('a').first().focus();
                }
            });


            // Click overlay close
            $close.on( "click", function(event) {
                if(Drupal.behaviors.resizer.isMobile){
                    // if third level close
                    if($(this).prev().hasClass('third-open')){
                        Drupal.behaviors.navigation.closeAllThird();
                    }else{
                        Drupal.behaviors.navigation.closeMenuOverlay();
                    }
                }else{
                    Drupal.behaviors.navigation.closeMenuOverlay();
                }

                Drupal.behaviors.navigation.mainLevelFocus.focus();

                $('html').removeClass('overlayed');

                event.preventDefault();
                event.stopPropagation();
            });


            // Search/Weather Toggles
            $('.toggle').click( function(event){

                var $type = $(this).data('type');

                Drupal.behaviors.navigation.mainLevelFocus = $(this);

                if(Drupal.behaviors.resizer.isMobile || Drupal.behaviors.resizer.isTablet){
                    $('html').addClass('overlayed');
                }

                $('.overlay').hide();
                $('#'+$type+'-overlay').attr('aria-hidden','false').fadeIn().focus();
                if($type == 'search'){
                    if(!Drupal.behaviors.resizer.isMobile && !Drupal.behaviors.resizer.isTablet){
                        $('#search-block-form input[type="search"]').focus();
                    }
                }

                if(Drupal.behaviors.resizer.isMobile){
                    Drupal.behaviors.navigation.closeMobileMenu();
                }

                // Nano scroll on overlay
                $('.nano').nanoScroller();

                event.preventDefault();
                event.stopPropagation();

            });
            $('#search-overlay .close, #weather-overlay .close'). click( function(event){

                Drupal.behaviors.navigation.closeUtilityOverlays();

                Drupal.behaviors.navigation.mainLevelFocus.focus();

                event.preventDefault();
                event.stopPropagation();

            });


            // Restrict tabbing to overlay modals when open
            $('.sub-menu').each( function(){
                var submenu = $(this);
                Drupal.behaviors.navigation.catchKeys(submenu,submenu.find('ul.menu').first().find('a').first());
            });
            Drupal.behaviors.navigation.catchKeys($('#search-overlay'),$('#search-overlay input[type="search"]'));
            Drupal.behaviors.navigation.catchKeys($('#weather-overlay'),$('#weather-overlay .converter'));


            // Close utility modals with esc key
            $('.utility-overlay').keydown(function(e) {
                if ( e.keyCode === 27 ) {
                    Drupal.behaviors.navigation.closeUtilityOverlays();
                    Drupal.behaviors.navigation.mainLevelFocus.focus();
                }
            });

        });

    };


    Drupal.behaviors.navigation.openSubMenu = function( $mainNavItem ){

        var $submenu = $mainNavItem.text();

        switch($submenu){
            case 'Explore':
                $submenu = 'explore';
                break;
            case 'Ski & Ride':
                $submenu = 'ski-ride';
                break;
            case 'Bike & Hike':
                $submenu = 'bike-hike';
                break;
            case 'Golf':
                $submenu = 'golf';
                break;
            case 'Events & Things To Do':
                $submenu = 'events-things-to-do';
                break;
            case 'Places To Stay':
                $submenu = 'places-to-stay';
                break;
        }

        // Open clicked menu
        if(Drupal.behaviors.resizer.isMobile){
            $('#menu-'+$submenu).addClass('is-active').show();
        }else{
            // Close active menu
            Drupal.behaviors.navigation.closeSubMenu();
            // Close Search/Weather
            Drupal.behaviors.navigation.closeUtilityOverlays();
            // Show menu and set aria
            $('#menu-overlay').fadeIn().attr('aria-hidden','false').attr('aria-labelledby',$('#menu-'+$submenu+' h2').attr('id'));
            $('#menu-'+$submenu).addClass('is-active').attr('aria-hidden','false').attr('tabindex','0').fadeIn().focus();
        }

        // Add active class
        $mainNavItem.addClass('is-active');

    };


    Drupal.behaviors.navigation.closeMenuOverlay = function( ) {

        if(Drupal.behaviors.resizer.isMobile) {
            $('body').removeClass('mobile-second-open');
        }else{
            // Close overlay
            $('#menu-overlay').fadeOut();
        }
        // Close sub menu
        Drupal.behaviors.navigation.closeSubMenu();

    };


    Drupal.behaviors.navigation.closeSubMenu = function( ){

        // Close active menu
        $('.sub-menu.is-active').fadeOut().removeClass('is-active').attr('aria-hidden','true').removeAttr('tabindex');

        // Remove aria tags
        $('#menu-overlay').attr('aria-hidden','true').attr('aria-labelledby','');

        // Remove active class
        Drupal.behaviors.navigation.mainMenu.find('.is-active').removeClass('is-active');

        // Close third
        Drupal.behaviors.navigation.closeAllThird();

    };


    Drupal.behaviors.navigation.closeAllThird = function( ){

        // Close active menu
        $('.sub-menu .third-open').removeClass('third-open').find('.is-active').removeClass('is-active');

        // Remove active class
        Drupal.behaviors.navigation.mainMenu.find('.is-active').removeClass('is-active');

    };


    Drupal.behaviors.navigation.closeMobileMenu = function( ){

        $('#menu-overlay,.sub-menu').attr('style','');
        $('#nav-toggle a').removeClass('close');
        $('body').removeClass('mobile-second-open');
        Drupal.behaviors.navigation.closeSubMenu();
        Drupal.behaviors.navigation.mainMenu.fadeOut();
        Drupal.behaviors.navigation.closeAllThird();

    };


    Drupal.behaviors.navigation.closeUtilityOverlays = function( ) {

        // Close overlays
        $('#search-overlay, #weather-overlay').fadeOut().attr('aria-hidden','true');

        $('html').removeClass('overlayed');

    };


    Drupal.behaviors.navigation.resetNavigation = function( ) {

        // Remove inline styles/classes
        $('#main-nav,#menu-overlay,.sub-menu').attr('style','');
        $('body').removeClass('mobile-second-open');
        $('#nav-toggle a').removeClass('close');
        // Close navs
        Drupal.behaviors.navigation.closeAllThird();

    };


    Drupal.behaviors.navigation.catchKeys = function(overlay, firstelem){

        // Send focus to close button on pressing shift + tab on overlay when it is focused
        overlay.keydown(function(e) {
            if (overlay.is(":focus")) {
                if (e.keyCode === 9 && e.shiftKey) {
                    e.preventDefault();
                    overlay.find('.close').focus();
                }
            }
        });

        // Send focus to close button on pressing shift + tab on first element
        firstelem.keydown(function(e) {
            if (e.keyCode === 9 && e.shiftKey) {
                e.preventDefault();
                overlay.find('.close').focus();
            }
        });

        // Send focus to first element on pressing tab on close
        overlay.find('.close').keydown(function(e) {
            if (e.keyCode === 9  && !e.shiftKey) {
                e.preventDefault();
                firstelem.focus();
            }
        });

    };



	/* --------------------------------------------------- */
    /* SIDE NAV
    /* --------------------------------------------------- */
    
    Drupal.behaviors.sideNav = {};
    Drupal.behaviors.sideNav.body = $('body');
    Drupal.behaviors.sideNav.sidebar = $('#sidebar');
    Drupal.behaviors.sideNav.scrollDirection = '';
    Drupal.behaviors.sideNav.scrollOffset = 0;
    Drupal.behaviors.sideNav.init = true;
    Drupal.behaviors.sideNav.scrolledUp = false;

    // sideNav attach function
    Drupal.behaviors.sideNav.attach = function (context, settings) {

        Drupal.behaviors.sideNav.checkSideNav();

        // Check sideNav on resize
        $(window).resize(function(event) {
            Drupal.behaviors.sideNav.checkSideNav();
        });

        var $scrollTop = $(document).scrollTop();

        $(window).scroll(function(event) {

            // Check sidenav
            Drupal.behaviors.sideNav.checkSideNav();

            // Determine scroll direction
            var $newScrollTop = $(this).scrollTop();

            if ($newScrollTop > $scrollTop){
                Drupal.behaviors.sideNav.scrollDirection = 'down';
            } else {
                Drupal.behaviors.sideNav.scrollDirection = 'up';
            }

            // Set var with scrolled amount
            Drupal.behaviors.sideNav.scrollOffset = Math.abs($newScrollTop - $scrollTop);

            // Reset scroll top to new position
            $scrollTop = $newScrollTop;

        });


    };


    // Fix/Absolute position based on scroll position
    Drupal.behaviors.sideNav.checkSideNav = function( ) {

        var $windowHeight = $(window).height();
        var $headerHeight = $('#header').outerHeight();
        var $pageTitleHeight = $('#page-title').outerHeight();
        var $mainHeight = $('#main').outerHeight();
        var $sidebarHeight = Drupal.behaviors.sideNav.sidebar.outerHeight();
        var $sidebarWidth = $('#main-wrap').width() / 4;
        var $sidebarHeaderHeight = $sidebarHeight + $headerHeight;
        var $scrollPosition = $(document).scrollTop();
        var $offsetTop = $sidebarHeaderHeight - $windowHeight + $pageTitleHeight;
        var $sidebarTop = 0;
        var $newPos = 0;

        // if desktop and mainheight larger than sidebar height
        if(Drupal.behaviors.resizer.isDesktop && $mainHeight > $sidebarHeight){

            // if sidebar is shorter than window
            if($sidebarHeaderHeight < $windowHeight){

                // if sidebar is scroller off the page
                if($scrollPosition > $pageTitleHeight) {

                    Drupal.behaviors.sideNav.fixSideNav($sidebarWidth, $headerHeight);

                }else{

                    // If scrolled back to sidebar inital position
                    if($scrollPosition < $pageTitleHeight + $headerHeight){
                        Drupal.behaviors.sideNav.resetSideNav();
                    }

                }

                // if scroll has reached the point of sidenav overlapping footer
                if($scrollPosition > $mainHeight + $pageTitleHeight - $sidebarHeight) {
                    Drupal.behaviors.sideNav.absoluteSideNav();
                }

            }
				
            // if side bar is taller than window
            if($sidebarHeaderHeight > $windowHeight){

                // if sidebar has scrolled to the highest point
                if($scrollPosition > $offsetTop){

                    // Set on initial scroll
                    if(Drupal.behaviors.sideNav.init){
                        Drupal.behaviors.sideNav.fixSideNav($sidebarWidth,-($sidebarHeight - $windowHeight));
                    }

                    Drupal.behaviors.sideNav.init = false;
                    $sidebarTop = parseInt(Drupal.behaviors.sideNav.sidebar.css('top'));

                    // If scrolling up
                    if(Drupal.behaviors.sideNav.scrollDirection == 'up'){
                        Drupal.behaviors.sideNav.scrolledUp = true;
                        // Get scroll offset and combine with current top pos
                        $newPos = $sidebarTop + Drupal.behaviors.sideNav.scrollOffset;
                        // If new position is greater than sidebar initial position
                        if($newPos > $headerHeight){
                            $newPos = $headerHeight;
                        }
                        // Set new position to match amount scrolled
                        Drupal.behaviors.sideNav.fixSideNav($sidebarWidth,$newPos);

                    }

                    // If has scrolled back up
                    if(Drupal.behaviors.sideNav.scrolledUp == true){
                        // If scrolling down
                        if(Drupal.behaviors.sideNav.scrollDirection == 'down') {
                            // Get scroll offset and combine with current top pos
                            $newPos = $sidebarTop - Drupal.behaviors.sideNav.scrollOffset;
                            // If new position is less than max sidebar scroll
                            if($newPos < -($sidebarHeight - $windowHeight)){
                                $newPos = -($sidebarHeight - $windowHeight);
                            }
                            // Set new position to match amount scrolled
                            Drupal.behaviors.sideNav.fixSideNav($sidebarWidth,$newPos);
                        }
                    }

                }else{

                    // Reset initial scroll
                    Drupal.behaviors.sideNav.init = true;

                    // If scrolled back to sidebar initial position
                    if($scrollPosition < $pageTitleHeight){
                        Drupal.behaviors.sideNav.resetSideNav();
                    }

                }

                // if scroll has reached the point of sidenav overlapping footer
                if($scrollPosition > ($mainHeight + $pageTitleHeight + $headerHeight) - $windowHeight) {
                    Drupal.behaviors.sideNav.resetSideNav();
                    // Set position absolute
                    Drupal.behaviors.sideNav.absoluteSideNav();
                    // Reset initial scroll
                    Drupal.behaviors.sideNav.init = true;
                }

            }


        } else{

            Drupal.behaviors.sideNav.resetSideNav();

        }

    };

    Drupal.behaviors.sideNav.fixSideNav = function( $width, $top ) {
        Drupal.behaviors.sideNav.body.addClass('sidebar-fixed');
        Drupal.behaviors.sideNav.body.removeClass('sidebar-absolute');
        Drupal.behaviors.sideNav.sidebar.css('top',$top+'px').css('width',$width+'px');
    };

    Drupal.behaviors.sideNav.absoluteSideNav = function( ) {
        Drupal.behaviors.sideNav.body.addClass('sidebar-absolute');
        Drupal.behaviors.sideNav.body.removeClass('sidebar-fixed');
        Drupal.behaviors.sideNav.body.removeClass('sidebar-scroll');
        Drupal.behaviors.sideNav.sidebar.attr('style','');
    };

    Drupal.behaviors.sideNav.resetSideNav = function( ) {
        Drupal.behaviors.sideNav.body.removeClass('sidebar-fixed');
        Drupal.behaviors.sideNav.body.removeClass('sidebar-absolute');
        Drupal.behaviors.sideNav.body.removeClass('sidebar-scroll');
        Drupal.behaviors.sideNav.sidebar.attr('style','');
    };



})(jQuery, Drupal, drupalSettings);
