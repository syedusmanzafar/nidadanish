{if $addons.ab__product_fe05_facebook_ads.fb_pixel_id}
    <!-- Facebook Pixel Code -->
    <script data-no-defer>
        {literal}
        !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
                n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
                t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
            document,'script','https://connect.facebook.net/en_US/fbevents.js');
        {/literal}
        fbq('init', '{$addons.ab__product_fe05_facebook_ads.fb_pixel_id}');
        fbq('track', 'PageView');

    {if $ab__pfe05_pixel}
        {foreach $ab__pfe05_pixel as $item}
        fbq('track', '{$item.event|escape:"javascript"}', {$item.data|json_encode nofilter});
        {/foreach}
    {/if}
    </script>
    <!-- End Facebook Pixel Code -->
    <noscript>
        <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={$addons.ab__product_fe05_facebook_ads.fb_pixel_id}&ev=PageView&noscript=1" />
    </noscript>
{/if}
