
{foreach from=$_params item="tab" key="tab_name"}
  <div id="content_{$tab_name}">  
  	
      {foreach from=$tab item="field" key="field_name"}
         {if $field.type == 'input'}
              <div class="control-group" id="container_elm_{$field_name}">
                  <label for="elm_{$field_name}" class="control-label{if $field.required} cm-required{/if}">{__("`$prefix`.{$field_name}")}                  
                      {if $field.tooltip}{include file="common/tooltip.tpl" tooltip=__("`$prefix`.`$field_name`_tooltip")}{/if}:
                  </label>
                  <div class="controls">
                      <input type="{if $field.min || $field.max}number{else}text{/if}" {if $field.min} min="{$field.min}"{/if} {if $field.max} max="{$field.max}"{/if} name="{$param_name}[{$field_name}]" id="elm_{$field_name}" size="55"
                             {if $field.class}class="{$field.class}"{/if}
                             {if $field.placeholder}placeholder="{$field.placeholder}"{/if}
                             value="{if isset($options.$field_name)}{$options.$field_name}{/if}"
                             {if $field.readonly} readonly{/if}
                             {if $disable_input} disabled{/if}
                             
                      />                     
                      {include file="buttons/update_for_all.tpl" display=$show_update_for_all object_id=$field_name name="update_all_vendors[`$field_name`]" hide_element="elm_`$field_name`"}
                      
                  </div>
                   {if $field.description}<p style="clear:both"><i>{$field.description nofilter}</i></p>{/if}
              </div>

          {elseif $field.type == 'checkbox'}
              <div class="control-group" id="container_elm_{$field_name}">
                  <label for="elm_{$field_name}" class="control-label{if $field.required} cm-required{/if}">{__("`$prefix`.{$field_name}")}
                      {if $field.tooltip}{include file="common/tooltip.tpl" tooltip=__("`$prefix`.`$field_name`_tooltip")}{/if}:
                  </label>
                  <div class="controls">
                      <input type="hidden" name="{$param_name}[{$field_name}]" value="N" {if $disable_input}disabled="disabled"{/if}>
                      <input type="checkbox" name="{$param_name}[{$field_name}]" id="elm_{$field_name}"
                          {if (isset($options.$field_name) && $options.$field_name == 'Y') } checked{/if} value="Y"
                              {if $field.readonly} readonly{/if}
                               {if $disable_input} disabled{/if}
                              />
                       {include file="buttons/update_for_all.tpl" display=$show_update_for_all object_id=$field_name name="update_all_vendors[`$field_name`]" hide_element="elm_`$field_name`"}                        
                  </div>
                   {if $field.description}<p style="clear:both"><i>{$field.description nofilter}</i></p>{/if}
              </div>
          {elseif $field.type == 'color'}
          
              <div class="control-group cmcs-colorpicker-wrapper" id="container_elm_{$field_name}">
                  <label for="elm_{$field_name}" class="control-label{if $field.required} cm-required{/if}">{__("`$prefix`.{$field_name}")}
                      {if $field.tooltip}{include file="common/tooltip.tpl" tooltip=__("`$prefix`.`$field_name`_tooltip")}{/if}:
                  </label>
                  <div class="controls">
                  		   
                     <input type="text" name="{$param_name}[{$field_name}]" id="elm_{$field_name}" size="55"
                             class="cmcs-colorpicker {if $field.class}{$field.class}{/if}"
                             {if $field.placeholder}placeholder="{$field.placeholder}"{/if}
                             value="{if isset($options.$field_name)}{$options.$field_name}{/if}"
                             {if $field.readonly} readonly{/if}
                             {if $disable_input} disabled style="background:{$options.$field_name}"{/if}                             
                      />
                       {include file="buttons/update_for_all.tpl" display=$show_update_for_all object_id=$field_name name="update_all_vendors[`$field_name`]" hide_element="elm_`$field_name`"}                        
                  </div>
                   {if $field.description}<p style="clear:both"><i>{$field.description nofilter}</i></p>{/if}
              </div>
          {elseif $field.type == 'selectbox'}
              <div class="control-group" id="container_elm_{$field_name}">
                  <label for="elm_{$field_name}" class="control-label{if $field.required} cm-required{/if}">{__("`$prefix`.{$field_name}")}
                      {if $field.tooltip}{include file="common/tooltip.tpl" tooltip=__("`$prefix`.`$field_name`_tooltip")}{/if}:
                  </label>
                  <div class="controls">
                      <select name="{$param_name}[{$field_name}]" id="elm_{$field_name}" {if $field.readonly} readonly{/if}
                       {if $field.class}class="{$field.class}"{/if}
                       {if $disable_input} disabled{/if}
                       >
                          {foreach from=$field.variants item="option_name" key="option_code"}
                              <option value="{$option_code}"
                                  {if (isset($options.$field_name) && $options.$field_name == $option_code)} selected="selected"{/if}>{__($option_name)}</option>
                          {/foreach}
                      </select>
                      {include file="buttons/update_for_all.tpl" display=$show_update_for_all object_id=$field_name name="update_all_vendors[`$field_name`]" hide_element="elm_`$field_name`"}
                  </div>
                  {if $field.description}<p style="clear:both"><i>{$field.description nofilter}</i></p>{/if}                        
              </div>
          {elseif $field.type == 'multiple'}
              <div class="control-group" id="container_elm_{$field_name}">
                  <label for="elm_{$field_name}" class="control-label{if $field.required} cm-required{/if}">{__("`$prefix`.{$field_name}")}
                      {if $field.tooltip}{include file="common/tooltip.tpl" tooltip=__("`$prefix`.`$field_name`_tooltip")}{/if}:
                  </label>
                  <div class="controls">
                
                      <select multiple name="{$param_name}[{$field_name}][]" id="elm_{$field_name}" {if $field.readonly} readonly{/if}
                       {if $field.class}class="{$field.class}"{/if}  {if $disable_input} disabled{/if}>
                          {foreach from=$field.variants item="option_name" key="option_code"}
                              <option value="{$option_code}"
                                  {if (isset($options.$field_name) && $option_code|in_array:$options.$field_name)} selected="selected"{/if}>{__("`$prefix`.`$option_name`")}</option>
                          {/foreach}
                      </select>
                      {include file="buttons/update_for_all.tpl" display=$show_update_for_all object_id=$field_name name="update_all_vendors[`$field_name`]" hide_element="elm_`$field_name`"}
                  </div>
                  {if $field.description}<p style="clear:both"><i>{$field.description nofilter}</i></p>{/if}                        
              </div>
          {elseif $field.type == 'template'}
              {include file=$field.template name_data="{$param_name}[{$field.name_data}]" data=$options[$field.name_data] params=$field.params}
          {elseif $field.type == 'title'}
              <h4 class="subheader" id="container_elm_{$field_name}">{__("`$prefix`.title_`$field_name`")}</h4>
              {if $field.description}<p style="clear:both"><i>{$field.description nofilter}</i></p>{/if}
          {elseif $field.type == 'func_info'}      	
          		<div class="control-group" id="container_elm_{$field_name}">
              		{$options.$field_name nofilter}  
                </div>
           {elseif $field.type == 'link'}      	
          		<div class="control-group" id="container_elm_{$field_name}">
              		<label for="elm_{$field_name}" class="control-label"></label>
                    <div class="controls">
                    	<a href="{$field.url|fn_url}" class="{$field.class}">{$field.name}</a>
                    </div>
                </div>
           {elseif $field.type == 'picker'}      	
          		<div class="control-group" id="container_elm_{$field_name}">
                	<label for="elm_{$field_name}" class="control-label{if $field.required} cm-required{/if}">{__("`$prefix`.{$field_name}")}
                      {if $field.tooltip}{include file="common/tooltip.tpl" tooltip=__("`$prefix`.`$field_name`_tooltip")}{/if}:
                  </label>
                  <div class="controls">                   	
                    {if $show_update_for_all} 
                    	<p>{__('text_select_vendor')}</p>
                    {else}                   	                
              		  {include_ext file="pickers/{$field.objects}/picker.tpl" company_ids='' data_id="objects_`$field_name`" input_name="`$param_name`[`$field_name`]" item_ids=$options.$field_name params_array=$field.params owner_company_id='0' but_meta="btn"}
                    {/if}                    
                    </div>
                </div>
          {elseif $field.type == 'order_status'} 
          		{$order_status_descr = $smarty.const.STATUSES_ORDER|fn_get_simple_statuses:true:true}
				{$order_statuses = $smarty.const.STATUSES_ORDER|fn_get_statuses:$statuses:true:true} 
                    	
          		<div class="control-group" id="container_elm_{$field_name}">
                	<label for="elm_{$field_name}" class="control-label{if $field.required} cm-required{/if}">{__("`$prefix`.{$field_name}")}
                      {if $field.tooltip}{include file="common/tooltip.tpl" tooltip=__("`$prefix`.`$field_name`_tooltip")}{/if}:
                  </label>
                  <div class="controls">
                  	{if $show_update_for_all} 
                    	<p>{__('text_select_vendor')}</p>
                    {else}                   	                
              		   {include file="common/select_popup.tpl"
                             suffix="o"                             
                             id=$field_name
                             status=$options.$field_name
                             items_status=$order_status_descr
                             update_controller="cuo"                           
                             status_target_id="container_elm_`$field_name`"                            
                             statuses=$order_statuses
                             btn_meta="btn btn-info o-status-`$options.$field_name` btn-small"|lower                            
                    	}
                    {/if}
                    </div>
                <!--container_elm_{$field_name}--></div>
                  
          {elseif $field.type == 'textarea'}
              <div class="control-group" id="container_elm_{$field_name}">
                  <label for="elm_{$field_name}" class="control-label{if $field.required} cm-required{/if}">{__("`$prefix`.{$field_name}")}
                      {if $field.tooltip}{include file="common/tooltip.tpl" tooltip=__("`$prefix`.`$field_name`_tooltip")}{/if}:
                  </label>
                  <div class="controls">
                      <textarea {if $field.readonly} readonly{/if} name="{$param_name}[{$field_name}]" id="elm_{$field_name}" {if $field.class}class="{$field.class}"{/if}
                       {if $disable_input} disabled{/if}
                      >{if isset($options.$field_name)}{$options.$field_name}{/if}</textarea>
                     {include file="buttons/update_for_all.tpl" display=$show_update_for_all object_id=$field_name name="update_all_vendors[`$field_name`]" hide_element="elm_`$field_name`"}                          
                  </div>
                   {if $field.description}<p style="clear:both"><i>{$field.description nofilter}</i></p>{/if}
              </div>       
          {/if}
         {if $field.show_when}
         	{foreach from=$field.show_when item=depend_fields key=root_field}
				<script>				
                    (function(_, $){
                        $.ceEvent('on', 'ce.commoninit', function(context) {		
                            fn_{$prefix}_check_{$field_name}();
                        });
                    })(Tygh, Tygh.$);                    
                    $(document).on('change', '#elm_{$root_field}', function(){
                        fn_{$prefix}_check_{$field_name}();
                    });	
					function fn_{$prefix}_check_{$field_name}(){						
						vals = {$depend_fields|json_encode nofilter};								
						{if $tab.$root_field.type=="checkbox"}							
							if ($.inArray($('#elm_{$root_field}:checked').val(), vals) >= 0){
							   $('#container_elm_{$field_name}').show();	
							}else{
							   $('#container_elm_{$field_name}').hide();		
							}							
						{else}											
							vals = {$depend_fields|json_encode nofilter}; 						
							if ($.inArray($('#elm_{$root_field}').val(), vals) >= 0){
							   $('#container_elm_{$field_name}').show();	
							}else{
							   $('#container_elm_{$field_name}').hide();		
							}						
						{/if}
					}
                </script>
         	{/foreach}
         {/if}
         {if $field.hide_when}
         	{foreach from=$field.hide_when item=depend_fields key=root_field}
				<script>				
                    (function(_, $){
                        $.ceEvent('on', 'ce.commoninit', function(context) {		
                            fn_{$prefix}_check_hide_{$field_name}();
                        });
                    })(Tygh, Tygh.$);                    
                    $(document).on('change', '#elm_{$root_field}', function(){
                        fn_{$prefix}_check_hide_{$field_name}();
                    });	
					function fn_{$prefix}_check_hide_{$field_name}(){						
						vals = {$depend_fields|json_encode nofilter};								
						{if $tab.$root_field.type=="checkbox"}							
							if ($.inArray($('#elm_{$root_field}:checked').val(), vals) >= 0){
							   $('#container_elm_{$field_name}').hide();	
							}else{
							   $('#container_elm_{$field_name}').show();		
							}							
						{else}											
							vals = {$depend_fields|json_encode nofilter}; 						
							if ($.inArray($('#elm_{$root_field}').val(), vals) >= 0){
							   $('#container_elm_{$field_name}').hide();	
							}else{
							   $('#container_elm_{$field_name}').show();		
							}						
						{/if}
					}
                </script>
         	{/foreach}
         {/if}
         
         
      {/foreach}
  </div>
  {/foreach}
  
  <script>
(function(_, $){
    $.ceEvent('on', 'ce.commoninit', function(context) {		
		$('input.cmcs-colorpicker:enabled').ceColorpicker();
	});
})(Tygh, Tygh.$);
$(document).on('click', '.cmcs-colorpicker-wrapper .cm-update-for-all-icon:not(.visible)', function(){
	try {			
		setTimeout(function(elm){
			$(elm).parent().find('input.cmcs-colorpicker:enabled').ceColorpicker();
		}, 100, $(this));
		
	}catch(err){
	}		
});
$(document).on('click', '.cmcs-colorpicker-wrapper .cm-update-for-all-icon.visible', function(){
	try {			
		$(this).parent().find('input.cmcs-colorpicker').ceColorpicker('destroy').attr('disabled', true);
	}catch(err){
	}		
});
  
  
  </script>