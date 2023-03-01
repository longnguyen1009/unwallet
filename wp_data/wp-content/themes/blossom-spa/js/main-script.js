jQuery(window).resize(function() {
    addMarginBottomHeader();
});

jQuery(document).ready(function () {
    addMarginBottomHeader();
});

function addMarginBottomHeader() {
    jQuery('.site-header').next().css("margin-top", jQuery('.site-header').outerHeight() + 'px');
}