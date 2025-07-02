jQuery(document).ready(function($) {
    // Tabbed navigation
    $('.euc-tabs-container .nav-tab-wrapper a').on('click', function(e) {
        e.preventDefault();
        var target = $(this).attr('href');

        // Update tab links
        $(this).addClass('nav-tab-active').siblings().removeClass('nav-tab-active');

        // Update tab content
        $(target).addClass('euc-tab-content-active').siblings().removeClass('euc-tab-content-active');
    });

    // Accordion functionality
    $('.euc-accordion-title').on('click', function() {
        $(this).parent('.euc-accordion-item').toggleClass('euc-accordion-open');
    });

    // Check/Uncheck All functionality
    $('.euc-toggle-all').on('change', function() {
        var isChecked = $(this).is(':checked');
        $(this).closest('.euc-accordion-content').find('input[type="checkbox"]').not(this).prop('checked', isChecked);
    });
});
