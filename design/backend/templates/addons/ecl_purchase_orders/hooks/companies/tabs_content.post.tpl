<div id="content_payment_terms" class="hidden">
	{if $auth.user_type == 'A'}
		<div class="control-group">
		    <label class="control-label">{__("payment_terms")}:</label>
		    <div class="controls">
		        <input type="text" name="company_data[payment_terms]" size="32" value="{$company_data.payment_terms}" class="input-large" />
		    </div>
		</div>
	{/if}

	{if $auth.user_type == 'V'}
		<div class="control-group">
		    <label class="control-label">{__("payment_terms")}:</label>
		    <div class="controls">
		        <p>{$company_data.payment_terms}</p>
		    </div>
		</div>
	{/if}
</div>