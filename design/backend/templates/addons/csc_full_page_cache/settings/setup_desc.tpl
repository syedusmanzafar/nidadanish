<div class="control-group" style="width:calc(100% - 180px)">
    <label class="control-label">{__("information")}:</label>
    <div class="controls">
        {__('csc.general_info_about_full_page_cache')}
    </div>
</div>

<div class="control-group" style="width:calc(100% - 180px)">
    <label class="control-label">{__("csc.cron_setup")}:</label>
    <div class="controls">
        {__('csc.general_info_cron_setup')}
        {assign var=controllers value=""|fn_csc_full_page_cache_get_cache_controllers}
        {assign var=controllers_string value = "=Y&"|implode:$controllers}
       	<div class="cfpc-cmd">/usr/bin/php {$smarty.const.DIR_ROOT}/{$config.admin_index}  --dispatch=full_page_cache.cron_clear --cron_key={$addons.csc_full_page_cache.cron_key} --products=Y --categories=Y --expired=Y</div>
        <p>-{__('or')}-</p>
        <div class="cfpc-cmd">wget -q "{"full_page_cache.cron_clear?cron_key=`$addons.csc_full_page_cache.cron_key`&`$controllers_string`=Y&expired=Y"|fn_url:"A"}" >/dev/null 2>&1 </div>
        
        
        
        </p>
        
    </div>
</div>