{foreach $ecl_orders as $ecl_order key="key"}
<div style="position: relative; display: block;">
	<div style="display: inline-block">
		<img src="https://www.nidadanish.com/design/backend/media/images/Nidadanish Logo-2.png" width="200" height="100" alt="Nidadanish Logo-2" style="display: block; padding-bottom:20px;">
	</div> 

	<div style="display: inline-block; position: absolute; top: 25%; right: 0; float: right; text-align: right;">
		<div style="font-family: Helvetica, Arial, sans-serif; padding-right: 10px;">Nida & Danish Trading LTD</div>
		<div style="font-family: Helvetica, Arial, sans-serif; padding-right: 10px; white-space: nowrap;">PO Box 65063, Aggrey St, Kariakoo, Dar es Salaam, Tanzania</div>
		<div style="font-family: Helvetica, Arial, sans-serif; padding-right: 10px;">Mob: +255 755050412</div>
		<div style="font-family: Helvetica, Arial, sans-serif; padding-right: 10px;">TIN: 133-318-488</div>
		<div style="font-family: Helvetica, Arial, sans-serif; padding-right: 10px;">VRN: 40-027756-G</div>
	</div>
</div>
{$vendor_id = $key}
<div style="font-family: Helvetica, Arial, sans-serif; padding-right: 10px;">{__("date")}:{$ecl_order_info_print.timestamp|date_format:"%d/%m/%Y"}</div>
<div style="font-family: Helvetica, Arial, sans-serif; padding-right: 10px; padding-bottom: 20px;">PO ID:{$key}{$random}</div>
<div style="font-weight:bold;font-size: 22px; font-family: Helvetica, Arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; padding-bottom: 20px; white-space: nowrap;">{__("purchase_order")}</div>
<div style="font-family: Helvetica, Arial, sans-serif; margin-top: 10px;">{__("Vendor")}:{$ecl_order.company_name}</div>
<div style="font-family: Helvetica, Arial, sans-serif; margin-top: 10px;">{__("contact_information")}:{$ecl_order.company_contact}</div>
<div style="font-family: Helvetica, Arial, sans-serif; margin-top: 10px;">{__("payment_terms")}:{$ecl_order.payment_terms}</div>
	<table width="100%" cellpadding="0" cellspacing="1" style="direction: {$language_direction}; background-color: #808080; margin-top: 20px;">
		<tr>
	    	<th width="70%" style="font-family: Helvetica, Arial, sans-serif;background-color: #eeeeee; padding: 6px 10px; white-space: nowrap;border: 1px solid #808080;">{__("product")}</th>
	    	<th style="font-family: Helvetica, Arial, sans-serif; background-color: #eeeeee; padding: 6px 10px; white-space: nowrap;border: 1px solid #808080;">{__("sku")}</th>
	    	<th style="font-family: Helvetica, Arial, sans-serif;background-color: #eeeeee; padding: 6px 10px; white-space: nowrap;border: 1px solid #808080;">{__("quantity")}</th>
	    	<th style="font-family: Helvetica, Arial, sans-serif;background-color: #eeeeee; padding: 6px 10px; white-space: nowrap;border: 1px solid #808080;">{__("unit_price")}</th>
	    	<th style="font-family: Helvetica, Arial, sans-serif;background-color: #eeeeee; padding: 6px 10px; white-space: nowrap;border: 1px solid #808080;">{__("subtotal")}</th>
		</tr>
	{foreach $ecl_order.orders as $ecl_products}
		{foreach $ecl_products.products  as $ecl_product}
		{$ecl_list_price = db_get_field("SELECT list_price FROM ?:products WHERE product_id =?i", $ecl_product['product_id'])}
		     <tr>
		        <td style="font-family: Helvetica, Arial, sans-serif;padding: 5px 10px; background-color: #ffffff;border: 2px solid #808080;">
		            {$ecl_product.product|default:__("deleted_product") nofilter}
		            {if $product.product_options}<br/>{include file="common/options_info.tpl" product_options=$ecl_product.product_options skip_modifiers=true}{/if}
		        </td>
		        <td style="font-family: Helvetica, Arial, sans-serif;padding: 5px 10px; background-color: #ffffff; text-align: left;border: 1px solid #808080;">{$ecl_product.product_code}</td>
		        <td style="font-family: Helvetica, Arial, sans-serif;padding: 5px 10px; background-color: #ffffff; text-align: center;border: 1px solid #808080;">{$ecl_product.amount}</td>
		        <td style="font-family: Helvetica, Arial, sans-serif;padding: 5px 10px; background-color: #ffffff; text-align: center;border: 1px solid #808080;">{$ecl_list_price}</td>

		        {$ecl_price_for_all = ($ecl_list_price * $ecl_product.amount)}
		        <td style="font-family: Helvetica, Arial, sans-serif;padding: 5px 10px; background-color: #ffffff; text-align: center;border: 1px solid #808080;">{$ecl_price_for_all}</td>
		    </tr>
	   {/foreach}
	{/foreach}
	</table>
	<div style="display: inline-block; width: 100%;">
		<div style="float: right">
{* 			<div style="margin-bottom: 10px;">
	        	<span style="font-size: 14px; color: #444; font-family: Helvetica, Arial, sans-serif;">{__("subtotal")}</span>
	            <span style="font-size: 14px; color: #444; font-family: Helvetica, Arial, sans-serif;">{$currencies.USD.symbol}&nbsp;{$list_price.$vendor_id.list_price}</span>
        	</div> *}
        	<div style="margin-bottom: 10px;">
	        	<span style="font-size: 14px; color: #444; font-family: Helvetica, Arial, sans-serif;">{__("tax")}</span>
	            <span style="font-size: 14px; color: #444; font-family: Helvetica, Arial, sans-serif;">Incl. VAT</span>
            </div>
            <div>
				<span style="font-weight:bold;font-size: 22px; border-top: 1px solid #e8e8e8; color: #444; font-family: Helvetica, Arial, sans-serif;">{__("mobile_app.mobile_total")}</span>
				<span style="font-weight:bold;font-size: 22px; border-top: 1px solid #e8e8e8; color: #444; font-family: Helvetica, Arial, sans-serif;">&nbsp;{$currencies.USD.symbol}&nbsp;{$ecl_total_price.$vendor_id.ecl_total_price}</span>
			</div>
		</div>
	</div>
	<div {if $key != array_key_last($ecl_orders)} style="page-break-after: always;"{/if}>
{* 		<div>Comments / Special Instructions</div>
		<textarea  cols="40" rows="5">{$ecl_order.customer_notes}</textarea> *}
    </div>
    <div style="font-weight:bold;font-size: 12px; font-family: Helvetica, Arial, sans-serif; text-transform: uppercase; color: #000000; white-space: nowrap;">Approved by  NidaDanish Admin</div>
{/foreach}

       
