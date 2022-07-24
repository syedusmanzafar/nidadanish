(function (_, $) {
    $.ceEvent('one', 'ce.commoninit', function () {
        $(_.doc).on('change', '.js-update-positions', function (event) {
            const positions = event.target.value;

            $.ceAjax('request', fn_url('sd_labels.position_update'), {
                method: 'post',
                data: {
                    positions: positions
                },
            });
        });
    });
})(Tygh, Tygh.$);