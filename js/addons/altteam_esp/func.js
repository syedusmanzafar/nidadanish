(function(_, $) {

	$(document).ready(function() {

		if (esp_data.up_everywhere == 'Y') {
			fn_show_up_button();
		}


		//	[history]
		//	Helper History Class
		var ContentHistory = function( firstElm, contenetClass ) {

			var contenetClass 		= contenetClass || 'scroll_pagination',		// 	content container class name
				firstElm 			= firstElm || $('.ty-pagination-container'),// 	first DOM container
				focusPage 			= esp_data.req_page,						//	scroll focus on this pagination page
				currentUrls 		= {},										//	load page urls
				contentCoordinate 	= {},										//	lower block coordinate elm.offset().top + elm.height()
				currentUrlOrig		= _.current_url,							//	save origin url
				currentUrl			= _.current_url,							//	save current url without hash
				continueSymbol		= '',										//	for new history url (? or &)

				methods =  {

					setFocusUrl: function( focusCurrentUrl ) {

						if (!focusCurrentUrl) { return false; };

						focusCurrentUrl = '' + focusCurrentUrl;

						$.ceHistory('load', focusCurrentUrl, {});
						currentUrl = focusCurrentUrl;
						_.current_url = focusCurrentUrl;

					},

					setFocusUrlHash: function( page ) {
						methods.setFocusUrl( currentUrl + '#focus_on=' + page );
					},

					setCurrentUrl: function( page, url ) {

						if ( !page || !url) { return false; };

						currentUrls[page] = url;

					},

					// create, save and set a new URL with the last_page parameter
					setCurrentUrlLastPage: function( page ) {

						var url = '' + currentUrlOrig + continueSymbol + 'last_page=' + page;
						methods.setCurrentUrl(  page, url );
						methods.setFocusUrl( url );

					},

					setContentCoordinate: function( page, coordinate ) {

						if ( !page || !coordinate) { return false; };

						contentCoordinate[page] = coordinate;

					},

					setContentCoordinates: function() {

						var contaners = $( '.' + contenetClass );

						if ( contaners.length <= 0 ) { return false; };

						contaners.each(function() {

							var self = $(this);
							var page = self.attr('rev');

							methods.setContentCoordinate(page, Math.ceil(self.offset().top + self.height()));

						});
					},

					checkContentCoordinate: function( scrollTop ) {

						var scrollTop = scrollTop || $(window).scrollTop() + $(window).height() / 2,	// center browser window
							page = 1;		//	new focus page

						$.each(contentCoordinate, function(index, val) {

							if ( scrollTop < val ) { return false; };

							page = ++index;

						});

						// if ( focusPage != page ) {

						focusPage = page;

						// change browser URL
						// [FIXME] bad hash URL concatenation
						methods.setFocusUrlHash( focusPage );

						// };
					}
				}

			//	[Init]

			//	[FIXME] hardcode
			// if (_.current_url.indexOf('page-') + 1) { $.scrollToElm( firstElm ); };

			//	remove 'last_page' from url
			if ( ~currentUrlOrig.indexOf('last_page') != 0 ) {

				if ( ~currentUrlOrig.indexOf('?last_page') != 0 ) {

					currentUrlOrig = currentUrlOrig.replace(/\?last_page=(\d+)/i, '');
					continueSymbol = '?';
				} else {

					currentUrlOrig = currentUrlOrig.replace(/&last_page=(\d+)/i, '');
					continueSymbol = '&';

				}

			} else {
				continueSymbol = ( ~currentUrlOrig.indexOf('?') != 0 ) ? '&' : '?';
			}

			methods.setCurrentUrl( focusPage, _.current_url );
			methods.setContentCoordinate( focusPage, Math.ceil( firstElm.offset().top + firstElm.height() ) );
			//	[/Init]

			return methods;

		}

		//	[/history]

		if (typeof $('.ty-pagination-container').attr('id') == 'string' &&
			typeof $('.ty-pagination > a[data-ca-target-id=pagination_contents]').attr('href') !== 'undefined' &&
			(esp_data.only_chosen == 'N' || (esp_data.only_chosen == 'Y' && $('#use_esp').val() == 'Y'))
		) {

			//	[FIXME]add this functionality for all
			var focusLatestCoordinate = $(window).scrollTop();	//	use this variables to increase scrolling step processing

			// [FIXME] Take into account the large containers when upload stories (latest_page))
			// var scrollingStep = $('.ty-pagination-container').height() / 5;							//	Scrolling step (px)
			var scrollingStep = 100;							//	Scrolling step (px)
			//	[/FIXME]add this functionality for all

			//	[history]
			var history = new ContentHistory();
			//	[/history]

			lang = esp_data.lang;
			container_id = $('.ty-pagination-container').attr('id');
			pagination_elm = $('.ty-pagination-container');
			flag = true;
			new_elm = pagination_elm;
			var ajax_c_url;
			part_for_scroll = 1/2;
			elment_width = {
				price: '64',
				product: '500'
			};
			var matches = [];
			aj_regex_all = new RegExp('<script[^>]*>([\u0001-\uFFFF]*?)Dropdown states([\u0001-\uFFFF]*?)</script>', 'img');
			if ($('.cm-dropdown-title')) {
				dropdown_wrap = $('.cm-dropdown-title');
			};

			$('.ty-pagination', pagination_elm).hide();
			if (esp_data.flag_watch_more == 'Y') {
				$('<div id="watch_more" class="watch_more">' + lang.watch_more + '</div>').appendTo(pagination_elm);
			};

			$(window).scroll(function() {

				//	[FIXME] separate discrete steps functionality for pagination & history
				//	discrete steps
				if ( Math.abs( $(window).scrollTop() - focusLatestCoordinate ) > scrollingStep ) {

					focusLatestCoordinate = $(window).scrollTop();

					//	[/FIXME] separate this functionality for pagination & history

					//	[history]
					// [FIXME] add hash focus for scroll (not actualy, use JQuery history)
					// history.checkContentCoordinate();
					//	[/history]

					fn_run_scroll_pagination();

				};

			});

			$('.ty-product-filters__block, .ty-sort-container, .ty-sort-dropdown, .ty-sort-container__views-icons').on("click mousedown mouseup", fn_run_scroll_pagination());

			if (esp_data.up_everywhere == 'N') {
				fn_show_up_button();
			}
		}


		function fn_click_watch_more() {
			$('#watch_more').click(function() {
				if ($('#watch_more').is(':visible') != false) {
					$(this).hide();
					$('body,html').animate({
						scrollTop: $(window).scrollTop() + 1,
						scrollTop: $(window).scrollTop() - 1
					}, 10);
					return false;
				}
			});
		}

		function fn_show_up_button()
		{
			var show_up_button = false;
			$(window).scroll(function() {

				if (!show_up_button && $(window).scrollTop() > 0) {
					$('#up_button').show();
					show_up_button = true;
				} else {
					if ($(window).scrollTop() == 0 && show_up_button) {
						$('#up_button').hide();
						show_up_button = false;
					}
				}
			});

			$('#up_button').click(function() {
				$('body,html').animate({
					scrollTop: 0
				}, 400);
				return false;
			});

			return false;
		}

		function abs( mixed_number )  {
			return ( ( isNaN ( mixed_number ) ) ? 0 : Math.abs ( mixed_number ) );
		}

		function fn_show_watch_more(flag_watch_more, new_elm) {
			if (typeof $('#watch_more').attr('id') == 'undefined' && flag_watch_more == 'Y') {
				$('<div id="watch_more" class="watch_more">' + lang.watch_more + '</div>').appendTo(new_elm);
			}
		}

		function fn_run_scroll_pagination() {
			if (typeof $('.ty-pagination-container').attr('id') == 'string' && typeof $('.ty-pagination > a[data-ca-target-id=pagination_contents]').attr('href') !== 'undefined') {

				pagination_elm = $('.ty-pagination-container');
				flag = true;
				var new_container = $(".scroll_pagination:last");
				if(new_container.length === 0) {
					new_container = $('.ty-pagination-container');
				}

				new_elm = pagination_elm | $('.ty-pagination-container');
				part_for_scroll = 1/2;
				elment_width = {
					price: '64',
					product: '500'
				};
				var matches = [];
				aj_regex_all = new RegExp('<script[^>]*>([\u0001-\uFFFF]*?)Dropdown states([\u0001-\uFFFF]*?)</script>', 'img');
				if ($('.cm-dropdown-title')) {
					dropdown_wrap = $('.cm-dropdown-title');
				};

				$('.ty-pagination', pagination_elm).hide();

				if ($(new_elm).attr('class') != 'scroll_pagination') {
					new_elm = $('.ty-pagination-container');
				}
				if ($('.ty-pagination', pagination_elm).is(':visible') == true) {
					$('.ty-pagination', pagination_elm).hide();
				}
				if (typeof $('form .table.products', pagination_elm).attr('class') !== 'undefined' && $('form .table.products', pagination_elm).css('margin-bottom') != '0px') {
					$('form .table.products', pagination_elm).css({'margin-bottom' : '0'});
				}
				if (flag == false && typeof $('.scroll_pagination').attr('rev') == 'undefined') {
					flag = true;
				}
				fn_show_watch_more(esp_data.flag_watch_more, new_elm);
				fn_click_watch_more();
				if (pagination_elm.attr('id') == container_id && (($(window).scrollTop() + $(window).height()) > ((new_elm.offset().top + new_elm.height()) - (new_container.height() * part_for_scroll))) && flag == true && $('#watch_more').is(':visible') == false) {

					if (esp_data.flag_watch_more == 'Y') {
						$('#watch_more').hide();
					}
					if (typeof $('.scroll_pagination').attr('rev') == 'undefined' && $('form', $('.ty-pagination-container')).attr('name') == 'short_list_form') {
						var widh_elm = $('form table:eq(0)  tr:eq(0) th', $('.ty-pagination-container'));
						$(widh_elm).each(function(i){
							if ($(widh_elm).eq(i).html() == lang.price) {
								$(widh_elm).eq(i).css({'width': elment_width.price});
							} else if ($(widh_elm).eq(i).html() == lang.product) {
								//							for stabilization
							} else {
								$(widh_elm).eq(i).css({'width': $(widh_elm).eq(i).width()});
							}
						});

						$('form table', $('.ty-pagination-container')).css({'table-layout' : 'fixed'});
					}

					if (typeof $('.scroll_pagination').attr('rev') !== 'undefined') {
						number_page = eval($('.scroll_pagination:last').attr('rev'));
					} else {
						// number_page = esp_data.req_page;
						number_page = $(".ty-pagination__selected", new_elm).html();
					}
					number_page ++;

					if (typeof $('.ty-pagination > a[data-ca-page=' + number_page + ']', new_elm).attr('href') !== 'undefined') {
						if (typeof $('.scroll_pagination').attr('rev') !== 'undefined') {
							ajax_c_url = $('.ty-pagination > a[data-ca-page=' + number_page + ']', new_elm).attr('href');
						} else {
							ajax_c_url = $('.ty-pagination > a[data-ca-page=' + number_page + ']', $('.ty-pagination-container')).attr('href');
						}
					} else {
						flag = false;
					}

					if (flag == true) {
						pagination_elm.attr('id', 'scroll_pagination_temp');
						$('form table', new_elm).css({'border-bottom' : 'none'});
						$('.pagination-bottom', new_elm).hide();
						$('<div class="scroll_pagination" rev="' + number_page + '" id="' + container_id + '"><div class="loading-items">' + lang.loaded_automatically + '</div></div>').appendTo(pagination_elm);

						$.ceAjax('request', ajax_c_url, {
							result_ids: container_id,
							pre_processing: function(aj_data, aj_params) {
								if (aj_data.html) {
									for (var k in aj_data.html) {
										matches = aj_data.html[k].match(aj_regex_all);

										aj_data.html[k] = matches ? aj_data.html[k].replace(aj_regex_all, '') : aj_data.html[k];

									}
								};},
							callback: function() {
								new_elm = $('.scroll_pagination:last');

								if (!new_elm.html()) {
									flag = false;
								} else {
									$('.ty-pagination', new_elm).hide();
									if ($('#sw_select_wrap_sort_by', new_elm)) {
										$('#sw_select_wrap_sort_by', new_elm).parent().empty();
									}
									if ($('#sw_select_wrap_view_as', new_elm)) {
										$('#sw_select_wrap_view_as', new_elm).parent().empty();
									}
									if ($('form', new_elm).attr('name') == 'short_list_form') {
										$('hr', new_elm).eq(0).hide();
										$('form table', new_elm).css({'table-layout' : 'fixed', 'border-top' : 'none', 'margin-top' : '0', 'margin-bottom' : '0'});
										$('form table tr', new_elm).eq(0).remove();

										var proportion_elm = $('table:first tr:last td', pagination_elm);

										$(proportion_elm).each(function(i){
											$('table tr td', new_elm).eq(i).css({'width': proportion_elm.eq(i).width()});
										});
									}
									//for cs-cart 3.0.1
									if ($('.sort-container', new_elm)) {
										$('.sort-container', new_elm).remove();
									}
									if ($('.ty-sort-container', new_elm)) {
										$('.ty-sort-container', new_elm).remove();
									}
								}
								new_elm.attr('id', 'scroll_pagination_' + number_page);
								pagination_elm.attr('id', container_id);
								if (esp_data.flag_watch_more == 'Y' && ((number_page - 1) % esp_data.num_watch_more == 0)) {
									$('#watch_more').appendTo(new_elm);
									$('#watch_more').show();
								}

								//	[history]

								//	re-check coordinate
								// history.setContentCoordinates();

								//	save new page url
								// history.setCurrentUrl(number_page, ajax_c_url);
								history.setCurrentUrlLastPage(number_page);

								//	[/history]

							}});
					}
				} else {
					new_elm = $('.ty-pagination-container');
				}
			}
		}


	});

}(Tygh, Tygh.$));