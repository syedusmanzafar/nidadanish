(function(_, $){

    $(document).on('click', '.cm-ls-close-popup', function() {
        var parent_form = $(this).parents('form');
        $box = parent_form.find('.live-search-box.cm-popup-box');
        if ($box.length > 0) {
            $box.hide();
        }
    });

    $(_.doc).on('click', '.cm-ls-load-more', function() {
        var target_id = $(this).data('caTargetId');
        var link_href = $(this).data('caHref');
        var page = $(this).data('caPage');
        var total_pages = $(this).data('caTotalPages');
        var input_id = $(this).data('caInputId');
        var load_btn = $(this);
        var loader_id = '#cp_ls_ajax_loader' + input_id;

        $(loader_id).show();
        $.ceAjax('request', link_href, {
            data: {
                page: page, 
                load_more: true
            },
            hidden: true,
            callback: function(content) {
                $(loader_id).hide();

                if (page < total_pages) {
                    load_btn.data('caPage', page + 1);
                } else {
                    load_btn.hide();
                    load_btn.siblings('a.cp-ls-view-all').addClass('cp-ls-full');
                }

                if (content.text) {
                    $(content.text).children().appendTo('#' + target_id);
                }
            }
        });
    });

    $.ceEvent('on', 'ce.ajaxdone', function(elms, scripts, params, response_data, response_text) {
        if(response_data.highlight) {
            var searchArray = response_data.highlight.split(' ');
            $('.live-search-box.cm-popup-box').highlight(searchArray, { element: 'span', className: 'live-match-higthlight' });
        }
    });

    $.ceEvent('on', 'ce.commoninit', function(context) {
        $('.search-input, .ty-search-block__input', context).on('input propertychange click', function(action) {
            if($(this).parents('.cp-live-search-overlay').length > 0) {
                return false;
            }
            if (action.type == 'click') {
                var parent_form = $(this).closest('form');
                var popup_elem = parent_form.find('.live-search-box.cm-popup-box');
                if (popup_elem.length > 0) {
                    popup_elem.show();
                }
                return true;
            }

            id_class = $(this).attr('id');
            var id = id_class.replace("search_input", "");

            ls_go_search($(this), id);
        });

        $('.search-input, .ty-search-block__input', context).attr('autocomplete', 'off');
    });

    // Search motivation
    var typed;

    $(document).ready(function() {
        var elem = $('#search_input');
        if (typeof ls_search_motivation != 'undefined' && ls_search_motivation.length != 0
            && elem.length && elem.attr('value') == ''
        ) {
            elem.removeClass('cm-hint');
            elem.val('');
            elem.attr('name', 'q');
            var title = elem.attr('title');
            if (title.length > 0) {
                elem.attr('placeholder', title);
            }
            typed = new Typed('#search_input', {
                'strings': ls_search_motivation,
                'stringsElement': null,
                'backSpeed': 100,
                'typeSpeed': 200,
                'smartBackspace': false,
                'startDelay': 1000,
                //'shuffle': true,
                'backDelay': 2000,
                'loop': true,
                'loopCount': Infinity,
                'attr': 'placeholder',
                'contentType': null,
                //'showCursor': true,
                //'cursorChar': '|',
                'bindInputFocusEvents': true,
            });
        }
    });

    $(document).on('focus', '#search_input', function(context) {
        if (typeof typed != 'undefined') {
            typed.destroy();
        }
    });

})(Tygh, Tygh.$);

function ls_go_search(elm, id) {
    if(elm.val().length >= letters_to_start) {
        day = new Date;
        ls_cur_time = day.getTime();
        var loader_id = '#cp_ls_ajax_loader' + id;
        $(loader_id).show();
        setTimeout(function() {
            day = new Date;
            now = day.getTime();
            differ = now - ls_cur_time;
            if (differ >= ls_search_delay - 1) {
                $.ceAjax('request', fn_url('products.cp_live_search'), {
                    data: {
                        q: elm.val(), 
                        search_input_id: id, 
                        company_id: elm.attr('data-company')
                    }, 
                    method: 'get', 
                    result_ids: 'live_reload_box' + id, 
                    hidden: true,
                    force_exec: true,
                    callback: function() {
                        $(loader_id).hide();
                    }
                });
            }
        }, ls_search_delay);
    }
}

function fill_live_input(elm, val) {
    var input = $(elm).parents('.ty-search-block').find('input[type="text"].ty-search-block__input');
    input.val(val);
    input.trigger('input');
}
