//<![CDATA[

// $(window).load(function() {
$(document).ready(function() {   
    setTimeout( function(){
        ThemabilityquickView.initquickView();
        $(".themability_quickview-button").removeClass("d-none");
    }, 500 );
});

// // datetimepicker bootstrap issue fixed
// function quickview_bottom_margin(){
//     if(
//         jQuery('.themability_quickview-container .input-group').hasClass("date") === true ||
//         jQuery('.themability_quickview-container .input-group').hasClass("time") === true ||
//         jQuery('.themability_quickview-container .input-group').hasClass("datetime") === true 
//     ){
//         jQuery('.themability_quickview-container .product-right').addClass("qv_margin_bottom");
//     }
// }

var ThemabilityquickView = {

    'initquickView': function() {
        $('body').append('<div class="themability_quickview-container"></div>');
        $('.themability_quickview-container').load('index.php?route=extension/themability4quickview/module/themability_quickview|insertcontainer');
    },

    'addCloseButton': function() {
        $('.themability_quickview-wrapper').prepend("<a href='javascript:void(0);' class='themability_quickview-btn' onclick='ThemabilityquickView.closeButton()'>&times;</a>");
    },

    'closeButton': function() {
        $('.themability_quickview-overlay').hide();
        $('.themability_quickview-wrapper').hide().html('');
        $('.themability_quickview-loader').hide();
    },

    ajaxView: function(url) {
        if (url.search('route=product/product') != -1) {
            url = url.replace('route=product/product', 'route=extension/themability4quickview/module/themability_quickview');
        } else {
            url = 'index.php?route=product/themability_quickview/seoview&ourl=' + url;
        }

        $.ajax({
            url: url,
            type: 'get',
            beforeSend: function() {
                $('.themability_quickview-overlay').show();
                $('.themability_quickview-loader').show();
            },
            success: function(json) {
                if (json['success'] == true) {
                    $('.themability_quickview-loader').hide();
                    $('.themability_quickview-wrapper').html(json['html']);
                    ThemabilityquickView.addCloseButton();

                    const additional = $('html').attr('dir');
                    $('#quick-carousel').each(function() {
                        const items = $(this).data('items') || 3;
                        const sliderOptions = {
                            loop: false,
                            nav: true,
                            navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
                            dots: false,
                            items: items,
                            responsiveRefreshRate: 200,
                            responsive: {
                                0: { items: 1 },
                                300: { items: ((items - 2) > 1) ? (items - 2) : 1 },
                                320: { items: ((items - 1) > 1) ? (items - 1) : 1 },
                                426: { items: items },
                                481: { items: ((items + 1) > 1) ? (items + 1) : 1 },
                                768: { items: ((items - 1) > 1) ? (items - 1) : 1 },
                                1200: { items: items }
                            }
                        };
                        if (additional == 'rtl') sliderOptions['rtl'] = true;
                        $(this).owlCarousel(sliderOptions);

                    });

                    $('.themability_quickview-wrapper').show();

                    // $('#datetimepicker').datetimepicker({
                    //     pickTime: false
                    // });
                    // $('#datetime').datetimepicker({
                    //     pickDate: true,
                    //     pickTime: true
                    // });

                    // $('#Time').datetimepicker({
                    //     pickDate: false
                    // });

                }
                // quickview_bottom_margin();
            }

        });

    }
};
//]]>