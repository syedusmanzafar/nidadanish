{assign var=url value="https://www.cs-commerce.com/?addon_reviews={$addon}"}
<div class="{$addon}_reviews">
    <div class="sidebar-row ">      	
        <ul>
            <li class="stars"><a href="{$url}" target="_blank"><i></i><i></i><i></i><i></i><i></i></a>
            </li>
             <li class="rate-us"><a href="{$url}" target="_blank">{__("`$prefix`.rate_us")}</a></li> 
             <li class="rate-us"><a href="https://www.cs-commerce.com/" target="_blank">{__("`$prefix`.other_addons")}</a></li>
              <li class="rate-us"><hr/></li>
             <li class="rate-us"><font size="4" style="vertical-align:bottom">&#9993;</font> <a class="cm-dialog-opener cm-dialog-auto-size" data-ca-target-id="csc_contact_us" data-ca-dialog-title="{__("`$prefix`.feedback")}" style="color:#333">{__("`$prefix`.feedback")}</a></li>                 
        </ul>
    </div>
</div>

<div id="csc_contact_us" class="hidden" title="{__("`$prefix`.feedback")}">
	<form action="{""|fn_url}" method="post" class="form-vertical cm-ajax cm-ajax-full-render">    
    	<input type="hidden" name="result_ids" value="elm_csc_message_block" />
    	<input type="hidden" name="feedback[addon]" value="{$addon}" />       
       	<div class="control-group">
       		<div class="center"><i>{__("`$prefix`.feedback_info")}</i></div>          
        </div>
    	<div class="control-group" id="elm_csc_message_block">
            <label for="elm_csc_message" class="control-label cm-required">{__('message')}</label>
            <div class="controls">
            <textarea rows="6" id="elm_csc_message" name="feedback[message]" class="span8"></textarea>                
            </div>
        <!--elm_csc_message_block--></div>
         <div class="buttons-container">          
            {include file="buttons/save_cancel.tpl" but_name="dispatch[`$prefix`.feedback]" cancel_action="close" save=true but_text=__('send') but_meta="cm-dialog-closer"}
        </div>
    </form>
</div> 