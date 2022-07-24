(function(_, $){
    $.ceEvent('on', 'ce.commoninit', function(context) {
        if ($('#simple_search_id').length) {
            var search_id = $('#simple_search_id').val();
            if (search_id != '') {
                $('#pagination_contents').find('a').each(function(index) {
                    var url = $(this).prop('href');
                    if (url.length > 0 && !url.match('/search_id=/')) {
                        $(this).prop('href', $.attachToUrl(url, 'search_id=' + search_id));
                    }
                });
                $('#pagination_contents').find('form[name^="product_form_"]').each(function(index) {
                    var search_input = $(this).find('input[name="search_id"]');
                    if (!search_input.length) {
                        $(this).append('<input type="hidden" name="search_id" value="' + search_id + '">');
                    }
                });
            }
        }
    });
})(Tygh, Tygh.$);
