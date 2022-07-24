<ul id="account_info_links_{$block.snapping_id}">
{*if $auth.user_id}
    <li class="ty-footer-menu__item"><a href="{"orders.search"|fn_url}">{__("orders")}</a></li>
    <li class="ty-footer-menu__item"><a href="{"profiles.update"|fn_url}">{__("profile_details")}</a></li>
{else}
    <li class="ty-footer-menu__item"><a href="{"auth.login_form"|fn_url}" rel="nofollow">{__("sign_in")}</a></li>
    <li class="ty-footer-menu__item"><a href="{"profiles.add"|fn_url}" rel="nofollow">{__("create_account")}</a></li>
{/if*}

<li class="ty-footer-menu__item"><a href="{"pages.view&page_id=20"|fn_url}">{__("contact_us")}</a></li>
<li class="ty-footer-menu__item"><a href="{"pages.view&page_id=26"|fn_url}">FAQ</a></li>
<li class="ty-footer-menu__item"><a href="{"orders.search"|fn_url}">{__("orders")}</a></li>
<li class="ty-footer-menu__item"><a href="{"profiles.update"|fn_url}">{__("my_account")}</a></li>
<li class="ty-account-info__item ty-footer-menu__item">
    <a href="{"rma.returns"|fn_url}" rel="nofollow" class="ty-account-info__a">{__("return_requests")}</a>    
</li>
<li class="ty-footer-menu__item"><a href="{"product_features.view_all&filter_id=1"|fn_url}">{__("brands")}</a></li>





<!--account_info_links_{$block.snapping_id}--></ul>