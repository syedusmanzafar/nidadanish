
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
<div style="font-family: Helvetica, Arial, sans-serif; padding-right: 10px;">{__("date")}:{$ecl_order_info_print.timestamp|date_format:"%D"}</div>
<div style="font-family: Helvetica, Arial, sans-serif; padding-right: 10px; padding-bottom: 20px;">PO ID:{$ecl_order_info_print.company_id}{$random}</div>
<div style="font-weight:bold;font-size: 22px; font-family: Helvetica, Arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; padding-bottom: 20px; white-space: nowrap;">{__("purchase_order")}</div>
<div style="font-family: Helvetica, Arial, sans-serif; margin-top: 10px;">{__("Vendor")}:{$orders.name}</div>
<div style="font-family: Helvetica, Arial, sans-serif; margin-top: 10px;">{__("contact_information")}:{$orders.package_info.origination.phone}</div>
<div style="font-family: Helvetica, Arial, sans-serif; margin-top: 10px;">{__("payment_terms")}:{$orders.payment_terms}</div>
    <table width="100%" cellpadding="0" cellspacing="1" style="direction: {$language_direction}; background-color: #808080; margin-top: 20px;">
        <tr>
            <th width="70%" style="font-family: Helvetica, Arial, sans-serif;background-color: #eeeeee; padding: 6px 10px; white-space: nowrap;border: 1px solid #808080;">{__("product")}</th>
            <th style="font-family: Helvetica, Arial, sans-serif; background-color: #eeeeee; padding: 6px 10px; white-space: nowrap;border: 1px solid #808080;">{__("sku")}</th>
            <th style="font-family: Helvetica, Arial, sans-serif; background-color: #eeeeee; padding: 6px 10px; white-space: nowrap;border: 1px solid #808080;">{__("quantity")}</th>
            <th style="font-family: Helvetica, Arial, sans-serif;background-color: #eeeeee; padding: 6px 10px; white-space: nowrap;border: 1px solid #808080;">{__("unit_price")}</th>
            <th style="font-family: Helvetica, Arial, sans-serif; background-color: #eeeeee; padding: 6px 10px; white-space: nowrap;border: 1px solid #808080;">{__("price")}</th>
        </tr>
        {foreach $ecl_order_info_print.products as $ecl_orders}
        {$ecl_list_price = db_get_field("SELECT list_price FROM ?:products WHERE product_id =?i", $ecl_orders['product_id'])}
         <tr>
            <td style="padding: 5px 10px; background-color: #ffffff;border: 1px solid #808080;">
                {$ecl_orders.product|default:__("deleted_product") nofilter}
                {if $product.product_options}<br/>{include file="common/options_info.tpl" product_options=$ecl_orders.product_options skip_modifiers=true}{/if}
            </td>
            <td style="font-family: Helvetica, Arial, sans-serif;padding: 5px 10px; background-color: #ffffff; text-align: left;border: 1px solid #808080;">{$ecl_orders.product_code}</td>
            <td style="font-family: Helvetica, Arial, sans-serif;padding: 5px 10px; background-color: #ffffff; text-align: left;border: 1px solid #808080;">{$ecl_orders.amount}</td>
            <td style="font-family: Helvetica, Arial, sans-serif;padding: 5px 10px; background-color: #ffffff; text-align: center;border: 1px solid #808080;">{$ecl_list_price}</td>
            {$ecl_price_for_all = ($ecl_list_price * $ecl_orders.amount)}
            <td style="font-family: Helvetica, Arial, sans-serif;padding: 5px 10px; background-color: #ffffff; text-align: left;border: 1px solid #808080;">{$ecl_price_for_all}</td>
        </tr> 
        {/foreach}  
    </table>
    <div style="font-weight:bold;font-size: 12px; font-family: Helvetica, Arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; white-space: nowrap;">Approved by  NidaDanish Admin</div>
    <div style="display: inline-block; width: 100%">
        <div style="float: right;">
        	<div style="margin-bottom: 10px;">
	        	<span style="font-size: 14px; color: #444; font-family: Helvetica, Arial, sans-serif;">{__("subtotal")}:</span>
	            <span style="font-size: 14px; color: #444; font-family: Helvetica, Arial, sans-serif;">{$price_of_all}</span>
        	</div>
        	<div style="margin-bottom: 10px;">
	        	<span style="font-size: 14px; color: #444; font-family: Helvetica, Arial, sans-serif;">{__("tax")}:</span>
	            <span style="font-size: 14px; color: #444; font-family: Helvetica, Arial, sans-serif;">Incl. VAT</span>
            </div>
            <div>
	            <span style="font-weight:bold;font-size: 22px; border-top: 1px solid #e8e8e8; color: #444; font-family: Helvetica, Arial, sans-serif;">{__("mobile_app.mobile_total")}:</span>
	            <span style="font-weight:bold;font-size: 22px; border-top: 1px solid #e8e8e8; color: #444; font-family: Helvetica, Arial, sans-serif;">{$ecl_total_price}</span>
        	</div>
        </div>
    </div>

    <div style="display: inline-block; width: 100%">
{* 		<div>Comments / Special Instructions</div>
		<textarea  cols="40" rows="5">{$ecl_order.customer_notes}</textarea> *}
		{* <div style="font-weight:bold;font-size: 12px; font-family: Helvetica, Arial, sans-serif; text-transform: uppercase; color: #000000; padding-right: 10px; padding-bottom: 50px; white-space: nowrap;">Approved by  NidaDanish Admin</div> *}
    </div>
