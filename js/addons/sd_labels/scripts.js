(function(_, $) {
    function updateLabelsMargin() {
        const $productDetailContainer = $('.ty-product-block, .ty-product-bigpicture, .urb-product-bigpicture').eq(0);
        const $elementsOffsetLabels = $productDetailContainer.find('.cm-image-gallery-wrapper, .cm-image-gallery, .ab-vertical-thumbnails').eq(0);

        $.ceEvent('trigger', 'sdLabels.afterGetElementsToOffsetLabels', [$productDetailContainer, $elementsOffsetLabels]);

        if (!$elementsOffsetLabels.length) return;

        const $imageContainer = $productDetailContainer.find('.cm-preview-wrapper');
        const $labels = $productDetailContainer.find('.js-labels-update-margin');
        const containerOffset  = $imageContainer.offset();
        const containerHeight  = $imageContainer.outerHeight(true);
        const labelsOffset = {
            bottom: 0,
            left: 0
        };

        $elementsOffsetLabels.each(function(index, element) {
            const $element = $(this);

            if ($element.is(':hidden')) return;

            const elementOffset = $element.offset();
            const elementHeight = $element.outerHeight(true);
            const elementWidth  = $element.outerWidth(true);

            if (Math.round(containerOffset.top + containerHeight) <= Math.round(elementOffset.top)) {
                labelsOffset.bottom += Math.round(elementHeight);
            } else if (Math.round(containerOffset.left) >= Math.round(elementOffset.left)) {
                labelsOffset.left += Math.round(elementWidth);
            }
        });

        $labels.css({
            marginBottom: labelsOffset.bottom > 0 ? labelsOffset.bottom + 'px' : '',
            marginLeft: labelsOffset.left > 0 ? labelsOffset.left + 'px' : ''
        });

        return labelsOffset;
    }

    function toggleDisplayLabels() {
        const $productImagesGallery = $(_.doc).find('.owl-carousel.cm-preview-wrapper');
        if (!$productImagesGallery.length) return;

        /*
        * .js-hide-sd-labels - for customization
        * .sd-aspect-ratio - adaptation with add-on sd_youtube
        * .ab__vg-image_gallery_video - adaptation with add-on ab__video_gallery
        */
        const exceptionClassList = '.js-hide-sd-labels, .sd-aspect-ratio, .ab__vg-image_gallery_video';
        const $productDetailContainer = $('.ty-product-block, .ty-product-bigpicture').eq(0);
        const owlData = $productImagesGallery.data().owlCarousel;

        if (owlData) {
            const $activeCarouselItem = $(owlData.$owlItems[owlData.currentItem]);

            if ($activeCarouselItem.find(exceptionClassList).length) {
                $productDetailContainer.find('.js-labels').fadeOut(300);
            } else {
                $productDetailContainer.find('.js-labels').fadeIn(300);
            }
        }
    }

    function elementsMoveToHead($context) {
        const $elements = $('.js-move-to-head', $context);

        $elements.appendTo('head');
    }

    $.ceEvent('on', 'ce.commoninit', function($context) {
        elementsMoveToHead($context);
        updateLabelsMargin();

        setTimeout(function() {
            toggleDisplayLabels();
        }, 0);
    });

    $(window).resize(function() {
        setTimeout(function() {
            updateLabelsMargin();
        }, 300);
    });

    $.ceEvent('on', 'ce.ajaxdone', function() {
        updateLabelsMargin();
    });

    $.ceEvent('on', 'ce.product_image_gallery.image_changed', function() {
        setTimeout(function() {
            toggleDisplayLabels();
        }, 0);
    });
})(Tygh, Tygh.$);