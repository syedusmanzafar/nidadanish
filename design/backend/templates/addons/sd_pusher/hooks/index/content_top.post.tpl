{if $show_admins_online_content
    && $buttons} {** workaround for 404 **}
    <div class="admins-online-wrap">
        <h6 class="muted">{__("admins_online")}:</h6>
        <div id="sd_pusher_admins_online_block" class="admins-online-block">
            <span id="sd_pusher_admins_online_quantity">0</span>
            <span class="icon-user admins_online_icon sd-pusher-green"/>
        </div>
        <div id="sd_pusher_admins_online_list">
        </div>
    </div>
{/if}
