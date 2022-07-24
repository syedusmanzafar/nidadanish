(function(_, $) {
    $.ceEvent('on', 'ce.commoninit', function($context) {
        const $container = $context.find('.js-sortable-container');

        if ($container.length === 0) {
            return;
        }

        $container.each(function(index, el) {
            const sortable = $(this);
            const dataPrefixId = sortable.data('caDataPrefixId');
            const dataId = sortable.data('caDataId');
            const sortableItemClass = sortable.data('caSortableItemClass');

            sortable.sortable({
                tolerance: 'pointer',
                containment: sortable,
                cursor: 'grabbing',
                revert: 100,
                forceHelperSize: true,
                axis: 'y',
                items: '.' + sortableItemClass,
                update: function () {
                    const newFieldIdsOrder = $('#' + dataId)
                        .find('.' + sortableItemClass)
                        .toArray()
                        .map(function (row) {
                            const itemId = $(row).find('input.js-sortable-item-id').val();
                            return itemId;
                        })
                        .filter(function (itemId) {
                            return itemId;
                        })
                        .join(',');

                    $('#' + dataPrefixId + dataId + '_ids').val(newFieldIdsOrder).trigger('change');
                }
            });
        });
    });
})(Tygh, Tygh.$);
