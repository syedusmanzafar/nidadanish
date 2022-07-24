<script>
(function(_, $) {
$(_.doc).on('click', '.compatible-title', function (e) {
var ct = $(this).parent().find('div.compatible-text');
if (ct.hasClass('hidden')) ct.removeClass('hidden');
else ct.addClass('hidden');
});
$.ceEvent('on', 'ce.commoninit', function(context) {
var ab_am_events = {$abam_events|json_encode nofilter};
var available_updates = Object.keys(ab_am_events.available_updates).length;
if (available_updates){
var menu = $('.navbar-admin-top .nav-pills');
if (menu.find('.ab__addons_manager').length){
menu.find('.ab__addons_manager').parent().parent().find('a:first > b').before('<span title="{"ab__am.menu.available_updates"|__}" class="ab-am-available-updates"></span>');
menu.find('.ab__addons_manager').find('a.ab__am').append('<span title="{"ab__am.menu.available_updates"|__}" class="ab-am-available-updates">' + available_updates + '</span>');
}
var menu = $('.adv-buttons');
if (menu.find('.ab__am-menu').length){
menu.find('.ab__am-menu').children('a').append('<span title="{"ab__am.menu.available_updates"|__}" class="ab-am-available-updates"></span>');
if (menu.find('.ab__am-menu').find('.ab__am').length){
menu.find('.ab__am-menu').find('.ab__am').append('<span title="{"ab__am.menu.available_updates"|__}" class="ab-am-available-updates">' + available_updates + '</span>');
}
}
var li_a = $('#elm_developer_pages a:contains("AlexBranding")');
if (li_a.length){
if (li_a.find('.ab-am-available-updates').length){
li_a.find('.ab-am-available-updates').remove();
}
li_a.append('<span title="{"ab__am.menu.available_updates"|__}" class="ab-am-available-updates">' + available_updates + '</span>');
}
}
});
}(Tygh, Tygh.$));
</script>
