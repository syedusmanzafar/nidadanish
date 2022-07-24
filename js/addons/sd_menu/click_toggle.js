(function(_, $) {
    $.ceEvent('on', 'ce.commoninit', function(context) {
        var menuItem = '.ty-menu__item.click';

        $(window).resize(function() {
            if ($(menuItem).length && $(window).width() > 767) {
                toggleSubmenu(menuItem);
            }
        }).resize();

        function toggleSubmenu(elem) {
            if ($(elem).length) {
                $('.ty-menu__item-link.click').on('click', function(e) {
                    var $this = $(this),
                        menuElm = $this.parents(".cm-responsive-menu"),
                        isHorisontalMenu = menuElm.parent().hasClass("ty-menu-vertical") ? false : true;

                    if ($this.siblings('.ty-menu__submenu').length || $this.parent().siblings('.ty-menu__submenu').length) {
                        e.preventDefault();
                        $this.closest(elem).siblings('.menu-hover').removeClass('menu-hover');

                        if ($this.closest(elem).hasClass('menu-hover')) {
                            $this.closest(elem).removeClass('menu-hover');
                            e.stopPropagation();
                        } else {
                            $this.closest(elem).addClass('menu-hover');
                        }

                        if(isHorisontalMenu) {
                            var menuWidth = menuElm.outerWidth(),
                                menuOffset = menuElm.offset(),
                                menuItemElm = $this.siblings('.ty-menu__submenu');

                            $('.sd-menu__submenu-to-right').removeClass('sd-menu__submenu-to-right');
                            var submenu, position;

                            if(menuItemElm) {
                                if(typeof menuItemElm.offset()  !== "undefined") {
                                    menuWidth = menuWidth - (menuItemElm.offset().left - menuOffset.left)
                                }
                                submenu = $('.cm-responsive-menu-submenu', menuItemElm).first();

                                if(submenu.length) {
                                    submenu.css({visibility: "hidden", left: 0});
                                    position = submenu.outerWidth();
                                    if(position > menuWidth) {
                                        submenu.parent().addClass('sd-menu__submenu-to-right');
                                    }
                                    submenu.css({visibility: "", left: "auto"});
                                }
                            }
                        }
                    }
                });

                $('body').on('click', function(e) {
                    if ($('.menu-hover').length && !$(e.target).closest(menuItem).length && !$(e.target).closest('.ty-menu__menu-btn').length){
                        $('.menu-hover').removeClass('menu-hover');
                    }
                });

                $(elem).on('touchstart', function() {
                    if ($(window).width() > 767) {
                        $(elem).each(function() {
                            $(this).closest('li.cm-menu-item-responsive').removeClass('cm-menu-item-responsive');
                        });
                    }
                });

                $('.ty-menu__menu-btn').on('click', function() {
                    $(this).toggleClass('menu-hover');
                });
            }
        }
    });
})(Tygh, Tygh.$);