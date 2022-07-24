(function(_, $) {
    function moveTooltips($context) {
        const $labelTipsContent = $('.js-label-tooltip-content', $context);

        if (!$labelTipsContent.length) return;

        $labelTipsContent.appendTo('body');
    }

    function initTooltips($context) {
        const $labelTips = $('.js-label-tooltip-toggle', $context);

        if (!$labelTips.length) return;

        $labelTips
            .each(function() {
                const $labelTip = $(this);
                $labelTip.ceTooltip({
                    tip: '#sd_label_tooltip_' + $labelTip.data('sdLabelId'),
                    events: {
                        tooltip: 'mouseover, mouseout'
                    },
                    onShow: function() {
                        // An empty function is passed to prevent the execution of bad ceTooltip component code
                    }
                });
            })
            .on('touchstart', function() {
                $(this).data().tooltip.show();
            })
            .on('touchend', function() {
                $(this).data().tooltip.hide();
            });
    }

    $.ceEvent('on', 'ce.commoninit', function($context) {
        moveTooltips($context);
        initTooltips($context);
    });
})(Tygh, Tygh.$);