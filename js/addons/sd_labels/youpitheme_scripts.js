$.ceEvent('on', 'sdLabels.afterGetElementsToOffsetLabels', function($container, $elements) {
    const $helpTextBlock = $container.find('.ypi-text-image-zoom');
    if (!$helpTextBlock.length) return;
    $elements.push($helpTextBlock);
});