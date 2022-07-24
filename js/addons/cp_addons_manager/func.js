(function(_, $){
    
     $(document).on('click', '.cp-am-select-item', function() {
        var scrollTargetId = '';
        if (typeof $(this).data('caUrl') !== 'undefined') {
            scrollTargetId = $(this).closest('div').attr('id');
            $.ceAjax('request', $(this).data('caUrl'), {
                data: {
                    selected: $(this).data('caSelected'),
                    result_ids: $(this).data('caTargetId')
                }
            });
        }
        if (scrollTargetId != '') {
            $.ceEvent('on', 'ce.ajaxdone', function() {
                var offsetTop = $('#' + scrollTargetId).offset().top - 150;
                $('html, body').scrollTop(offsetTop);
            });
        }
    });
    
    
    $.ceEvent('on', 'ce.commoninit', function () {
        if ($('.cp-am-check-height').length && $('.cp-am-height-block').length) {
            var content_pos = $('.cp-am-check-height').offset().top;
            var content_height = $('.cp-am-check-height').height();
            var bottom_pos = $('.cp-am-height-block').offset().top;
            
            var extra_height = content_pos + content_height - bottom_pos;
            if (extra_height > 0) {
                $('.cp-am-height-block').css('height', extra_height + 'px');
            }
        }
    });

    $(_.doc).on('mouseover', '.cp-am-tooltip[title]', function() {
        var $el = $(this);
        if (!$el.data('tooltip')) {
            cpTooltip($el);
        }
        $el.data('tooltip').show();
    });
    
    function cpTooltip(elem, params) {
        var default_params = {
            events: {
                def: 'mouseover, mouseout',
                input: 'focus, blur'
            },
            layout: '<div class="cp-am-tooltip-content"></div>'
        };

        $.extend(default_params, params);

        return elem.each(function() {
            var elm = $(this);
            var params = default_params;

            if (elm.data('tooltip')) {
                return false;
            }

            if (elm.data('ceTooltipPosition') === 'top') {
                params.position = 'top left';
                params.tipClass = 'tooltip arrow-top';
                params.offset = [30, -7];

                if (_.language_direction == 'rtl') {
                    params.offset = [30, 7];
                    params.position = 'top right';
                }
            } else {
                params.offset = [-30, -7];
                params.tipClass = 'tooltip arrow-down';
                params.position = 'bottom left';

                if (_.language_direction == 'rtl') {
                    params.offset = [-30, 7];
                    params.position = 'bottom right';
                }
            }

            elm.tooltip(params).dynamic({
                right: {},
                left: {}
            });

            //hide tooltip before remove
            elm.on("remove", function() {
                $(this).trigger('mouseout');
            });
        });
    }
})(Tygh, Tygh.$);
