(function(_, $) {
    $.ceEvent('on', 'ce.commoninit', function(context) {
        $('.sd-amazon-menu .ty-menu__item-link').on('click',function(event) {
            if ($(this).parentsUntil($('.ty-menu__items'), '.ty-menu__item-nodrop').length === 0) {
                if ($(window).width() <= 767) {
                    event.preventDefault();
                }

                $(this).siblings('.ty-menu__item-toggle').click();
            }
        });

        $('.sd-amazon-menu .ty-menu__submenu-link').on('click', function(event) {
            if ($(this).closest($('.ty-menu__submenu-item-header')).siblings('.ty-menu__item-toggle').length != 0) {
                if ($(window).width() <= 767) {
                    event.preventDefault();
                }

                $(this).closest($('.ty-menu__submenu-item-header')).siblings('.ty-menu__item-toggle').click();
            }
        });
    });
})(Tygh, Tygh.$);