<div id="addon_upload_container">
    {if $non_writable}
    <form action="{""|fn_url}" method="post" name="addon_upload_form" class="form-horizontal cm-ajax" enctype="multipart/form-data">
        <input type="hidden" name="result_ids" value="addon_upload_container" />
        <input type="hidden" name="addon_extract_path" value="{$addon_extract_path}" />
        <input type="hidden" name="addon_name" value="{$addon_name}" />
        <input type="hidden" name="return_url" value="{"cp_addons_manager.manage"|fn_url}" />
        
    	<div class="control-group" id="non_writable_dirs">
            <strong class="text-error">{__("non_writable_directories")}:</strong>
            <ol class="text-error">
            {foreach $non_writable as $dir => $perm}
                <li>{$dir}</li>
            {/foreach}
            </ol>
        <!--non_writable_dirs--></div>

        <div>
            {include file="buttons/button.tpl" but_role="submit" but_text=__("recheck") but_name="dispatch[cp_addons_manager.recheck]"}
        </div>
        <hr>
    </form>
    <script type="text/javascript">
        (function(_, $) {
            $.ceEvent('one', 'ce.commoninit', function () {
                $.scrollToElm($('#non_writable_dirs'));
            });
        })(Tygh, Tygh.$);
    </script>
    {/if}
<!--addon_upload_container--></div>
