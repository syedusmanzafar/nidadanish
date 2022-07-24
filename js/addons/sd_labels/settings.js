(function (_, $) {
    $(document).ready(function(){
        $('.cm-update-for-all-icon[data-ca-hide-id=addon_option_sd_labels_text_label_round_corners]').on('click', function() {
            $('#addon_option_sd_labels_text_label_round_corners').toggleClass('disable-overlay-wrap');
            $('#addon_option_sd_labels_text_label_round_corners_overlay').toggleClass('disable-overlay');
        });
        $('.cm-update-for-all-icon[data-ca-hide-id=addon_option_sd_labels_text_label_corner_radius]').on('click', function() {
            $('#addon_option_sd_labels_text_label_corner_radius').toggleClass('disable-overlay-wrap');
            $('#addon_option_sd_labels_text_label_corner_radius_overlay').toggleClass('disable-overlay');
        });
    });
})(Tygh, Tygh.$);
