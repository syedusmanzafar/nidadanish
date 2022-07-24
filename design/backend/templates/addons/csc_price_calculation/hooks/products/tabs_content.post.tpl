<div id="content_csc_price_calculation" class="hidden">
{capture name="globals"}
<table class="table table-middle " width="100%" >
    <thead>
    <tr class="cm-first-sibling">
    	<th width="20%">{__("cpck.indexes")}</th>
    	 <th width="30%">{__("name")}</th>               
        <th width="20%">{__("value")}</th>       
        <th width="30%"></th>
    </tr>
    </thead>     
     <tbody class="hover">    
     {foreach from=$globals item="global"}     
    <input type="hidden" name="product_data[calculation][globals][{$global.id}][global_id]" value="{$global.id}"  />
     <tr>
   	   <td align="center">
       <a class="icon-plus" onclick="fn_add_value_to_formula('[glb_{$global.id}]')" title="{__("cpck.add_to_formula")}"></a>
       [glb_{$global.id}]
       
       </td>
        <td><input type="text" name="product_data[calculation][globals][{$global.id}][name]" value="{$global.name}" class="span5 " /></td>
             
<td>
          		 <input type="text" name="product_data[calculation][globals][{$global.id}][value]" value="{$global.value}" class="span5" />
            </td>
             <td class="right nowrap">
                <div class="hidden-tools">
                	{include file="buttons/multiple_buttons.tpl" item_id="feature_variants_`$global.id`" tag_level="3" only_delete="Y"}
                </div>
        </td>
        </tr>
        {/foreach}
        
   {assign var="num" value=1}
    <tr class="hover" id="box_add_variants_for_existing_">     
       <td>{__("cpck.add_new_global")}:</td>          
        <td>      
            <input type="text" name="product_data[calculation][new_global][{$num}][name]" value="" class="span5" />
         </td>
         <td>
          		 <input type="text" name="product_data[calculation][new_global][{$num}][value]" value="" class="span5" />
         </td>
            
         
       <td class="right">
            <div class="hidden-tools">
                {include file="buttons/multiple_buttons.tpl" item_id="add_variants_for_existing_" tag_level=1}
            </div>
        </td>
    </tr>
    
    </tbody>
    </table>
{/capture}

{include file="common/subheader.tpl" title=__("cpck.global_variables") target="#addon_global_variables"}
<div id="addon_global_variables" {*class="collapse in"*}>
{$smarty.capture.globals nofilter}
</div>


{include file="common/subheader.tpl" title=__("options") target="#addon_options_defaults"}

<div id="addon_options_defaults" class="collapse in">
{if $product_options}
    <table class="table table-middle" width="100%"> 
     <thead><tr class="cm-first-sibling">
     <th width="50%">{__("option_name")}</th>
         <th>{__('cpck.indexes')}</th>     
         
       </tr> </thead><tbody>
       
     {hook name="price_calculation:options_block"}
     
         {foreach $product_options as $opt}
            {if $opt.option_type!="I" && $opt.option_type!="X" && $opt.status=="A"}  
           <tr>   
            
            <td>{$opt.option_name} {if $opt.option_type=="N"}({__("number")}){/if} {if $opt.cur_product_id}&nbsp; &nbsp;  &nbsp;<b>({__('global')})</b>{/if}</td>
            <td>
            <a class="icon-plus cm-tooltip" onclick="fn_add_value_to_formula('[opt_{$opt.option_id}]')" title="{__("cpck.add_to_formula")} {if $opt.option_type=="S" || $opt.option_type=="R"}{__("cpck.weight_modifier")}{/if}"></a>
            [opt_{$opt.option_id}]</td>          
           </tr>
           {/if}
          {/foreach} 
               
	{/hook}
      
      </tbody>  
    </table>
{else}    
    <div class="control-group">
		{__("text_nothing_found")}
    </div>
 {/if}   
</div>


{include file="common/subheader.tpl" title=__("features") target="#addon_features"}
<div id="addon_features" class="collapse in">
{if $features}
<table class="table table-middle" width="100%"> 
 <thead><tr class="cm-first-sibling">
 	<th width="50%">Feature name</th>
     <th>{__('cpck.indexes')}</th>     
     <th>Value</th>     
   </tr> </thead>
 {foreach $features as $feature}  
  <tbody><tr>
 
    <td>{$feature.description}</td>
    <td><a class="icon-plus" onclick="fn_add_value_to_formula('[ftr_{$feature.feature_id}]')" title="{__("cpck.add_to_formula")}"></a>[ftr_{$feature.feature_id}]</td> 
    <td>{$feature.value_int}</td>     
   </tr></tbody> 
  {/foreach}  
</table>
{else}
	<div class="control-group">
		{__("text_nothing_found")}
    </div>
{/if}
</div>
{include file="common/subheader.tpl" title=__("cpck.formula") target="#addon_formula"}
<div id="addon_formula" class="collapse in">
    
	{hook name="csc_price_calculation:formula"}
	<div class="control-group">
     <label class="control-label" for="formula_status">{__("cpck.calculation_formula_status")}:</label>     
        <div class="controls">   
            <select name="product_data[formula][status]" id="formula_status">
                    <option value="D" {if $formula.status == "D"} selected="selected"{/if}>{__("disabled")}</option>
                    <option value="A" {if $formula.status == "A"} selected="selected"{/if}>{__("active")}</option>     
          </select>    	
        </div>
    </div>
    {/hook}
    
    <div class="control-group">
        <label class="control-label" for="product_data_formula">{__("cpck.calculation_formula")}:</label>     
        <div class="controls">
        {if $cpc_settings.mode=="S"}
            <input type="text" onkeyup="fn_csc_check_formula(this)" name="product_data[formula][formula]" id="product_data_formula" size="55" value="{$formula.formula}" class="input-large" />
         {else}
         <textarea name="product_data[formula][formula]" rows="8" cols="155" id="product_data_formula" class="input-large">{$formula.formula}</textarea>
         {/if}
        </div>
     </div>
     
     
     <div class="control-group">
     <label class="control-label" for="formula_example">{__("cpck.other_variables")}:</label>
      <div class="controls">
		 {hook name="csc_price_calculation:variables"}
         <p><a class="icon-plus" onclick="fn_add_value_to_formula('[price]')" title="{__("add_to_formula")}"></a> [price]</p>
         <p><a class="icon-plus" onclick="fn_add_value_to_formula('[list_price]')" title="{__("add_to_formula")}"></a> [list_price]</p>
         <p><a class="icon-plus" onclick="fn_add_value_to_formula('[amount]')" title="{__("add_to_formula")}"></a> [amount]</p>
          <p><a class="icon-plus" onclick="fn_add_value_to_formula('[in_stock]')" title="{__("add_to_formula")}"></a> [in_stock]</p>
          <p><a class="icon-plus" onclick="fn_add_value_to_formula('[popularity]')" title="{__("add_to_formula")}"></a> [popularity]</p>
		  {/hook}
        </div>
    </div> 
     
      <div class="control-group">
     <label class="control-label" for="formula_example">{__("cpck.formula_example")}:</label>
      <div class="controls">
      {if $cpc_settings.mode=="S"}
            <input type="text" class="input-large" id="formula_example" value="([price]+[opt_1])*[opt_2]*[opt_3]*[glb_1]" size="55" readonly />
       {else}
         <textarea rows="7" cols="155" id="formula_example" class="input-large" readonly>
$price = ([price]+[opt_1])*[opt_2]*[opt_3]*[glb_1];
if ($price>50){
  $price=$price*0.9;
}
if ([opt_1]>15){
  $price=$price*1.2;
}</textarea>
         {/if}
        </div>
    </div> 
     
</div>



</div>{*close tab div*}