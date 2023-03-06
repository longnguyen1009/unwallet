jQuery(window).resize(function() {
    addMarginBottomHeader();
});

jQuery(document).ready(function () {
    addMarginBottomHeader();
    jQuery('.owl-carousel').owlCarousel({
        loop:true,
        nav:true,
        navText : ['<span class="fa fa-chevron-circle-left" aria-hidden="true"></span>','<span class="fa fa-chevron-circle-right" aria-hidden="true"></span>'],
        dots:true,
        margin:15,
        autoplay:true,
        autoplayTimeout:4000,
        autoplayHoverPause:true,
        responsiveClass:true,
        lazyLoad:true,
        smartSpeed:1200,
        responsive:{
            0:{
                items:1,
                nav:true
            },
            // 768:{
            //     items:2,
            //     nav:true
            // },
            // 992:{
            //     items:4,
            //     nav:true,
            //     loop:false
            // }
        }
    });
});

function addMarginBottomHeader() {
    jQuery('.site-header').next().css("margin-top", jQuery('.site-header').outerHeight() + 'px');
}

