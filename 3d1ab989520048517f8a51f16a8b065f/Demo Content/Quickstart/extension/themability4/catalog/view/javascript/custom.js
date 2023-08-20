let prevWidth = null;

(function ($) {
    "use strict";

    /*----------
    Loader
    ----------*/
    $(window).on("load", function () {
        $('.loader').fadeOut('slow',function(){
            $(this).remove();
        });
    });
        
    $(document).ready(function() {
        // set column+content
        updateColumnsAndContent();
        // category page click events
        clickEventsInCategoryPage();

        // breadcrumb
        addbredcumb();
        // pagetitle
        setPageTitle();
        // Slider
        setProductCarousel();
        // responsive header
        responsiveheader();
        // footer
        footerExplanCollapse();

        $(window).resize(function () {
            // set column+content
            updateColumnsAndContent();
        });

    });

    $(window).resize(function(){
        responsiveheader();
    });

    /*----------
    Update column & content in responsive
    -----------*/
    function updateColumnsAndContent() {
        if ($(window).width() < 992) {
            $('#column-left, #column-right').insertAfter('#content');

            // menu
            if ($("#menu .dropdown.menulist .toggle-menu").length == 0) {
                $("#menu .dropdown.menulist").append("<span class='toggle-menu'><i class='fa fa-plus'></i></span>");
                $("#menu .dropdown.menulist .dropdown-submenu.sub-menu-item").append("<span class='toggle-menu'><i class='fa fa-plus'></i></span>");
                $('#topCategoryList ul.sub-menu').removeAttr("style");
                $('#topCategoryList div.dropdown-menu').removeAttr("style");
                $('#topCategoryList').hide();
                // call explan-collapse
                // responsiveMenuExpandCollapse();
            }

            // left, right
            $("#column-left .box-category .toggle-open, #column-right .box-category .toggle-open, #column-left .box-content .toggle-open, #column-right .box-content .toggle-open").remove();
            $("#column-left .box-category h3, #column-right .box-category h3, #column-left .box-content h3, #column-right .box-content h3").append("<span class='toggle-open'><i class='fa fa-chevron-down'></i></span>");
            $('#column-left ul.parent, #column-right ul.parent, #column-left .block_box, #column-right .block_box, #column-left .box-content ul, #column-right .box-content ul, #column-left .filter_box, #column-right .filter_box').hide();
    
            // footer
            if ($(".footer-top .toggle-open").length == 0) {
                $(".footer-top h2").append("<span class='toggle-open'><i class='fa fa-chevron-down'></i></span>");
                $('.footer-top ul.list-unstyled').hide();
            }
        } else {
            $('#column-right').insertAfter('#content');
            $('#column-left').insertBefore('#content');

            // menu
            $("#menu .dropdown.menulist .toggle-menu").remove();
            $('#topCategoryList').show();
            $('#topCategoryList ul.sub-menu').removeAttr("style");
            $('#topCategoryList div.dropdown-menu').css("display", "");

            // left, right
            $("#column-left .box-category .toggle-open, #column-left .box-content .toggle-open").remove();
            $("#column-right .box-category .toggle-open, #column-right .box-content .toggle-open").remove();
            $('#column-left ul.parent, #column-right ul.parent, #column-left .block_box, #column-right .block_box, #column-left .box-content ul, #column-right .box-content ul, #column-left .filter_box, #column-right .filter_box').show();

            // footer
            $(".footer-top .toggle-open").remove();
            $('.footer-top ul.list-unstyled').show();
        }
    }

    /*----------
    Breadcrumb
    ----------*/
    function addbredcumb() {
        $('.breadcrumb').wrap("<div class='breadcrumb-container'></div>");
        $('.breadcrumb-container').wrap("<div class='breadcrumb-main'><div class='container'></div></div>");
        $('#content > h2:first-child, #content > h1:first-child').insertBefore('.breadcrumb');
        $('.breadcrumb-main').insertAfter('header');
    }   
    $(function(){
        if ( $('ul.breadcrumb li').length == 2 ) {
            var last_val=$('ul.breadcrumb').children('li').last().text();
            var link_val =$('ul.breadcrumb li:last-child a').attr('href');
        }
    });

    /*----------
    Page Title
    ----------*/
    function setPageTitle() {
        $('.breadcrumb-main h2, .breadcrumb-main h1').addClass("page-title");        
    }

    /*----------
    Slider for product
    ----------*/
    function setProductCarousel() {
        const direction = $('html').attr('dir');
        $('.product-carousel').each(function () {
            if ($(this).closest('#column-left').length == 0 && $(this).closest('#column-right').length == 0) {
                $(this).addClass('owl-carousel owl-theme');
                const items = $(this).data('items') || 5;
                const sliderOptions = {
                    loop: false,
                    rewind: false,
                    autoplay: false,
                    autoplayTimeout: 3000,
                    nav: true,
                    mouseDrag: true,
                    touchDrag: true,
                    navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>'],
                    dots: false,
                    items: items,                   
                    responsiveRefreshRate: 200,                    
                    responsive: {
                        0: {
                            items: 1,
                            margin: 10
                        },
                        320: {
                            items: ((items - 3) > 1) ? (items - 3) : 1,
                            margin: 10
                        },
                        576: {
                            items: ((items - 2) > 1) ? (items - 2) : 1,
                            margin: 10
                        },
                        992: {
                            items: ((items - 1) > 1) ? (items - 1) : 1,
                            margin: 30
                        },
                        1441: { 
                            items: items,
                            margin: 30
                        }
                    }
                };
                if (direction == 'rtl') sliderOptions['rtl'] = true;
                $(this).owlCarousel(sliderOptions);
            }
        });        
    }

    /*----------
    Dropdown Toggle
    ----------*/
    $(function(){
        $(".search-content .search-btn-outer").on('click',function(){
            $(this).toggleClass('active');
            $(".header-search").slideToggle( "2000" );
            return false;
        });

        // Hide Search Dropdown On Scroll 
        $(window).scroll(function(){
            $('.ui-autocomplete.ui-widget-content').hide();
        });
    });

    /*----------
    Set header(991)
    ----------*/
    function responsiveheader(){
        var this_window_width = $(window).width();
        if (prevWidth != this_window_width) {
            if (this_window_width <= 991) {
                $('.header-center').insertBefore('.search-content');
            }
            else if (this_window_width <= 1199 && this_window_width >= 992 ) {
                $('.header-center').insertAfter('.header-inner');
            }
            else {
                $('.header-center').insertBefore('.header-right');            
            }

            if (this_window_width <= 767) {
                $('.html1 .banner-outer:nth-child(3)').insertBefore('.html1 .banner-outer:nth-child(2)');
            }
            else {
                $('.html1 .banner-outer:nth-child(2)').insertBefore('.html1 .banner-outer:nth-child(3)');            
            }
            prevWidth = this_window_width;
        }
    }

    /*----------
    Footer Toggle
    ----------*/
    function footerExplanCollapse() {
        $(".footer-top h2").addClass('toggled');
        $('.footer-top .toggled').on('click',function(e){
            e.preventDefault();
            if ($(window).width() < 992) {
                $(this).toggleClass('active');
                $(this).parent().find('ul').toggleClass('active').toggle('slow');
            }
        });
    }

    /*----------
    Category page click events
    ----------*/
    function clickEventsInCategoryPage() {
        $('.box-category .toggled').on('click',function(e){
            e.preventDefault();
            if ($(window).width() < 992) {
                $(this).toggleClass('active');
                $(this).parent().find('ul.parent').toggleClass('active').slideToggle('slow');
            }
        });

        $('#column-left .box-content .toggled').on('click',function(e){
            e.preventDefault();
            if ($(window).width() < 992) {
                $(this).toggleClass('active');
                if ($(this).parent().find('ul').length != 0) {
                    $(this).parent().find('ul').toggleClass('active').slideToggle('slow');
                } else {
                    $(this).parent().find('.filter_box').toggleClass('active').slideToggle('slow');
                    $(this).parent().find('.block_box').toggleClass('active').slideToggle('slow');
                }
            }
        });

        $('#column-right .box-content .toggled').on('click',function(e){
            e.preventDefault();
            if ($(window).width() < 992) {
                $(this).toggleClass('active');
                if ($(this).parent().find('ul').length != 0) {
                    $(this).parent().find('ul').toggleClass('active').slideToggle('slow');
                } else {
                    $(this).parent().find('.filter_box').toggleClass('active').slideToggle('slow');
                    $(this).parent().find('.block_box').toggleClass('active').slideToggle('slow');
                }
            }
        });
    }   

    /*----------
    Category page current active
    ----------*/
    $(function () {
        setNavigation();
    });
    function setNavigation() {
        var currentHref = window.location.href;
        $("#select-category li a").each(function () {
            var href = $(this).attr('href');
            if (currentHref === href) {
                if ($(this).parents('.has-more-category')) {
                    $(this).parents('.has-more-category').find('a.list-group-item.main-item').addClass('active');
                    $(this).parents('.has-more-category').find('.group').css("display","block");
                }
                $(this).addClass('active');
                $(this).parent().find('.group').css("display","block");
            }
        });
    }

    /*----------
    quantity seter
    ----------*/
    $( document ).on( 'click', '.plus, .minus', function( e ) {
        e.preventDefault();
        var parent = $( this ).parents( '.product-btn-quantity' );
        var quantity = parent.find( '[name="quantity"]' );
        var val = quantity.val();
        if ( $( this ).hasClass( 'plus' ) ) {
            val = parseInt( val ) + 1;
        } else {
            if(val == 1) {
                val = 1;
            }
            else {
                val = val >= 1 ? parseInt( val ) - 1 : 0;
            }
        }
        quantity.val( val );
        quantity.trigger("change");
        return false;
    });

    $(document).ready(function() {
        if($(window).width() > 991) {
            $("#prozoom").elevateZoom({
                zoomType: "inner",
                cursor: "crosshair",
                gallery:'additional-carousel',
                galleryActiveClass: 'active'
            });   
          
            var image_index = 0;
            $('#additional-carousel a').click(function() {
                var smallImage = $(this).attr('data-image');
                var largeImage = $(this).attr('data-zoom-image');
                var ez = $('#prozoom').data('elevateZoom');
                $('.product-content .thumbnail').attr('href', largeImage);
                ez.swaptheimage(smallImage, largeImage);
                image_index = $(this).index('#additional-carousel a');
                return false;
            });
        }
    });    

})(jQuery);