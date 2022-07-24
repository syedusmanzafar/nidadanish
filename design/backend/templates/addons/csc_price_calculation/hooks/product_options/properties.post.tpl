    <div class="hidden" id="content_tab_price_addon_variants_{$id}">
     <fieldset>
       <table class="table table-middle">
        <thead>
        <tr class="first-sibling">
            <th width="25%" >{__("default_value")}</th>
            <th width="25%">{__("cpck.min_value")}</th>
            <th width="25%">{__("cpck.max_value")}</th>
            <th width="25%">{__("cpck.step")}</th>
           
        </tr>
        </thead>
       
        <tbody class="hover cm-row-item" id="option_variants_{$id}_{$num}">
        <tr>
        	<td><input type="text" id="defaults_default_value" onkeyup="fn_check_values_on_csc_defaults(this)" name="option_data[csc_price][default_value]" value="{$option_data.csc_price.default_value}" size="5" class="input-medium" /></td>
            <td><input type="text" id="defaults_min" onkeyup="fn_check_values_on_csc_defaults(this)" name="option_data[csc_price][min]" value="{$option_data.csc_price.min}" size="5" class="input-medium" /></td>
        	<td><input type="text" id="defaults_max" onkeyup="fn_check_values_on_csc_defaults(this)" name="option_data[csc_price][max]" value="{$option_data.csc_price.max}" size="5" class="input-medium" /></td>
       		 <td><input type="text" id="defaults_step" onkeyup="fn_check_values_on_csc_defaults(this)" name="option_data[csc_price][step]" value="{$option_data.csc_price.step}" size="5" class="input-medium" /></td>
        </tr></tbody></table>
        {if $features}
         
        {include file="common/subheader.tpl" title=__("features") }
        	{__('cpck.csc_price_calc_features_on_options_description')}
            <div id="options_features" class="collapse in">           
            <table class="table table-middle" width="100%"><thead><tr class="cm-first-sibling">
                <th width="50%">Feature name</th><th>{__('indexes')}</th><th>Value</th></tr></thead>
             {foreach $features as $feature}  
              <tbody <tr>            
                <td>{$feature.description}</td>
                <td>[ftr_{$feature.feature_id}]</td> 
                <td>{$feature.value_int}</td>     
               </tr></tbody> 
              {/foreach}  
            </table>           
            </div>
        {/if}
    </fieldset>
    <!--content_tab_price_addon_variants_{$id}--></div>
    
    <div class="hidden" id="content_tab_price_variants_{$id}">
    {include file="common/subheader.tpl" title=__("variants")}
    {__('cpck.csc_price_calc_variants_description')}
    <fieldset>
        <table class="table table-middle">
        <thead>
        <tr class="first-sibling">
            <th class="cm-non-cb">{__("position_short")}</th>
            <th class="cm-non-cb">{__("name")}</th>        
            <th class="cm-non-cb">{__("value")}</th>
            <th class="cm-non-cb">{__("status")}</th>
            <th class="cm-non-cb"></th>
        </tr>
        </thead>
        {foreach from=$option_data.variants item="vr" name="fe_v"}
        {assign var="num" value=$smarty.foreach.fe_v.iteration}
        <tbody class="hover cm-row-item" id="option_variants_{$id}_{$num}">
        <tr>
            <td class="cm-non-cb{if $option_data.option_type == "C"} hidden{/if}">
                <input type="text" name="option_data[csc_variants][{$num}][position]" value="{$vr.position}" size="3" class="input-micro" /></td>
            <td class="cm-non-cb{if $option_data.option_type == "C"} hidden{/if}">
                <input type="text" name="option_data[csc_variants][{$num}][variant_name]" value="{$vr.variant_name}" class="input-medium" /></td>
            <td class="nowrap {if $runtime.company_id && $shared_product == "Y"} cm-no-hide-input{/if}">
                <input type="text" name="option_data[csc_variants][{$num}][modifier]" value="{$vr.modifier}" size="5" class="input-mini" />
                               
            </td>
         
            <td class="cm-non-cb{if $option_data.option_type == "C"} hidden{/if}">
                {include file="common/select_status.tpl" input_name="option_data[csc_variants][`$num`][status]" display="select" obj=$vr meta="input-small"}</td>
           
             <td class="right cm-non-cb{if $option_data.option_type == "C"} hidden{/if}">
                {include file="buttons/multiple_buttons.tpl" item_id="option_variants_`$id`_`$num`" tag_level="3" only_delete="Y"}
            </td>
        </tr>       
        </tbody>
        {/foreach}

        {math equation="x + 1" assign="num" x=$num|default:0}{assign var="vr" value=""}
        <tbody class="hover cm-row-item {if $option_data.option_type == "C"}hidden{/if}" id="box_add_csc_variant_{$id}">
        <tr>
            <td class="cm-non-cb{if $option_data.option_type == "C"} hidden{/if}">
                <input type="text" name="option_data[csc_variants][{$num}][position]" value="" size="3" class="input-micro" /></td>
            <td class="cm-non-cb{if $option_data.option_type == "C"} hidden{/if}">
                <input type="text" name="option_data[csc_variants][{$num}][variant_name]" value="" class="input-medium" /></td>
            <td>
                <input type="text" name="option_data[csc_variants][{$num}][modifier]" value="" size="5" class="input-mini" />
              
            </td>
           
            <td class="cm-non-cb{if $option_data.option_type == "C"} hidden{/if}">
                {include file="common/select_status.tpl" input_name="option_data[csc_variants][`$num`][status]" display="select" meta="input-small"}</td>
           
            <td class="right cm-non-cb{if $option_data.option_type == "C"} hidden{/if}">
                {include file="buttons/multiple_buttons.tpl" item_id="add_csc_variant_`$id`" tag_level="2"}
            </td>
        </tr>
        
        </tbody>
        </table>
    </fieldset>
    <!--content_tab_price_addon_variants_{$id}--></div>
    
<script type="text/javascript">
 (function (_, $) {
     $.ceEvent('on', 'ce.commoninit', function (context) {
		 $("#tab_option_details_{$id}").after('<li id="tab_price_addon_variants_{$id}" class="cm-js" {if $option_data.option_type != "N"} style="display:none"{/if}><a>{__("settings")}</a></li><li id="tab_price_variants_{$id}" class="cm-js" {if $option_data.option_type != "N"} style="display:none"{/if}><a>{__("variants")}</a></li>');	 
		 $('.cm-j-tabs').ceTabs();		 
		 $("#content_tab_price_variants_{$id}").detach().appendTo("#tabs_content_{$id}");
		 $("#content_tab_price_addon_variants_{$id}").detach().appendTo("#tabs_content_{$id}");
		 $("#elm_option_type_{$id}").append('<option value="N" {if $option_data.option_type == "N"}selected="selected"{/if}>{__("number")} ({__("addon")})</option>');		
		$("#elm_option_type_{$id}").on('change', function(){
			if ($(this).val()=="N"){
				 $("#tab_price_addon_variants_{$id}").show();
				 $("#tab_price_variants_{$id}").show();	
			}else{
				 $("#tab_price_addon_variants_{$id}").hide();
				 $("#tab_price_variants_{$id}").hide();		
			}		
		});
		 
		 
	 });
 }(Tygh, Tygh.$));
</script>

