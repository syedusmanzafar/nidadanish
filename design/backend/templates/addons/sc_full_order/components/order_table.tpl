<div class="table-responsive-wrapper">

    {*
    {if $order_info.is_sc_united_ship_order == "Y"}
    <div class="cp_one_order_border">  <p>{__("cp_united_shipping_test_vendor_order_address")}</p>

        <p>{__("vendor")}: <a href="{"companies.update&company_id=`$order_info.company_id`"|fn_url}">{$order_info.company_id|fn_get_company_name}</a></p>
    </div>
        {else}
        *}

    {if !$is_single}
      <div class="well cp-one-order__header">
        <div class="cp-one-order__header-lc">
            <div class="cp-one-order__combination">
                <a href="#" alt="{__("expand_collapse_list")}" title="{__("expand_collapse_list")}" id="on_cp_subitems_{$order_info.order_id}" class="cm-combination" style="display: none;">
                    <span class="icon-caret-right"> </span>
                </a>
                <a href="#" alt="{__("expand_collapse_list")}" title="{__("expand_collapse_list")}" id="off_cp_subitems_{$order_info.order_id}" class="cm-combination" >
                    <span class="icon-caret-down"> </span>
                </a>
            </div>
            <div class="cp-one-order__vendor">
                {__("vendor")}: <a href="{"companies.update&company_id=`$order_info.company_id`"|fn_url}">{$order_info.company_id|fn_get_company_name}</a>
            </div>
            <div class="cp-one-order__order">
                <a target="_blank"  href="{"orders.details?order_id={$order_info.order_id}"|fn_url}">{__("order")} #{$order_info.order_id} </a>
            </div>
            <div class="cp-one-order__tools">
                {$print_order = __("print_invoice")}
                {if $status_settings.appearance_type == "C" && $order_info.doc_ids[$status_settings.appearance_type]}
                    {$print_order = __("print_credit_memo")}
                {elseif $status_settings.appearance_type == "O"}
                    {$print_order = __("print_order_details")}
                {/if}

                {capture name="tools_list"}
                    {hook name="orders:details_tools"}
                        <li>{btn type="list" text=$print_order href="orders.print_invoice?order_id=`$order_info.order_id`" class="cm-new-window"}</li>
                    {if $settings.Appearance.email_templates === "new"}
                        <li>{btn type="list" text=__("edit_and_send_invoice") href="orders.modify_invoice?order_id=`$order_info.order_id`"}</li>
                    {/if}
                        <li>{btn type="list" text=__("print_packing_slip") href="orders.print_packing_slip?order_id=`$order_info.order_id`" class="cm-new-window"}</li>
                        <li>{btn type="list" text=__("edit_order") href="order_management.edit?order_id=`$order_info.order_id`"}</li>
                        <li>{btn type="list" text=__("copy") href="order_management.edit?order_id=`$order_info.order_id`&copy=1"}</li>
                        <li>
                            {btn type="list"
                            text=__("delete")
                            href="orders.delete?order_id={$order_info.order_id}&redirect_url=orders.manage"
                            class="cm-confirm"
                            method="POST"
                            }
                        </li>
                    {$smarty.capture.adv_tools nofilter}
                    {/hook}
                {/capture}
                {dropdown content=$smarty.capture.tools_list}
            </div>
        </div>

        

          <div class="cp-one-order__status">
                <span>{__("status")}:</span>
              {hook name="orders:order_status"}
              {$get_additional_statuses=true}
              {$order_status_descr=$smarty.const.STATUSES_ORDER|fn_get_simple_statuses:$get_additional_statuses:true}
              {$extra_status=$config.current_url|escape:"url"}
              {if "MULTIVENDOR"|fn_allowed_for}
                  {$notify_vendor=true}
              {else}
                  {$notify_vendor=false}
              {/if}

              {$statuses = []}
              {$order_statuses=$smarty.const.STATUSES_ORDER|fn_get_statuses:$statuses:$get_additional_statuses:true}
                  {include file="common/select_popup.tpl" suffix="o" id=$order_info.order_id status=$order_info.status items_status=$order_status_descr update_controller="orders" notify=true notify_department=true notify_vendor=$notify_vendor status_target_id="content_downloads" extra="&return_url=`$extra_status`" statuses=$order_statuses popup_additional_class="dropleft cp_status_one_order_admin" text_wrap=true}
              {/hook}
          </div>
      </div>
    {/if}
    {*/if*}

    {if !$is_single}
    <div class="control-group">

        <div class="controls">

        </div>
    </div>
    {/if}


    <table width="100%" class="table table-middle table--relative table-responsive" id="cp_subitems_{$order_info.order_id}">
        <thead>
        <tr>
            <th width="50%">{__("product")}</th>
            <th width="10%">{__("price")}</th>
            <th class="center" width="10%">{__("quantity")}</th>
            {if $order_info.use_discount}
                <th width="5%">{__("discount")}</th>
            {/if}
            {if $order_info.taxes && $settings.Checkout.tax_calculation != "subtotal"}
                <th width="10%">&nbsp;{__("tax")}</th>
            {/if}
            <th width="10%" class="right">&nbsp;{__("subtotal")}</th>
        </tr>
        </thead>


        {if $order_info.is_sc_united_ship_order == "Y"}

            <tr>
               <td data-th="{__("product")}"></td>

            <td class="nowrap" data-th="{__("price")}">
                {include file="common/price.tpl" value=$order_info.total}</td>
            <td class="center" data-th="{__("quantity")}">
               1
            </td>
            {if $order_info.use_discount}
                <td class="nowrap" data-th="{__("discount")}">
                    {if $oi.extra.discount|floatval}{include file="common/price.tpl" value=$oi.extra.discount}{else}-{/if}</td>
            {/if}
            {if $order_info.taxes && $settings.Checkout.tax_calculation != "subtotal"}
                <td class="nowrap" data-th="{__("tax")}">
                    {if $oi.tax_value|floatval}{include file="common/price.tpl" value=$oi.tax_value}{else}-{/if}</td>
            {/if}
            <td class="right" data-th="{__("subtotal")}"><span> {include file="common/price.tpl" value=$order_info.total}</span></td>


        {else}


        {foreach from=$order_info.products item="oi" key="key"}
            {hook name="orders:items_list_row"}
            {if !$oi.extra.parent}
                <tr>
                    <td data-th="{__("product")}">
                        <div class="order-product-image">
                            {include file="common/image.tpl" image=$oi.main_pair.icon|default:$oi.main_pair.detailed image_id=$oi.main_pair.image_id image_width=$settings.Thumbnails.product_admin_mini_icon_width image_height=$settings.Thumbnails.product_admin_mini_icon_height href="products.update?product_id=`$oi.product_id`"|fn_url}
                        </div>
                        <div class="order-product-info">
                            {if !$oi.deleted_product}<a href="{"products.update?product_id=`$oi.product_id`"|fn_url}">{/if}{$oi.product nofilter}{if !$oi.deleted_product}</a>{/if}
                            <div class="products-hint">
                                {hook name="orders:product_info"}
                                {if $oi.product_code}<p class="products-hint__code">{__("sku")}:{$oi.product_code}</p>{/if}
                                {/hook}
                            </div>
                            {if $oi.product_options}<div class="options-info">{include file="common/options_info.tpl" product_options=$oi.product_options}</div>{/if}
                        </div>
                    </td>
                    <td class="nowrap" data-th="{__("price")}">
                        {if $oi.extra.exclude_from_calculate}{__("free")}{else}{include file="common/price.tpl" value=$oi.original_price}{/if}</td>
                    <td class="center" data-th="{__("quantity")}">
                        {$oi.amount}<br />
                        {if !"ULTIMATE:FREE"|fn_allowed_for && $oi.shipped_amount > 0}
                            <span class="muted"><small>({$oi.shipped_amount}&nbsp;{__("shipped")})</small></span>
                        {/if}
                    </td>
                    {if $order_info.use_discount}
                        <td class="nowrap" data-th="{__("discount")}">
                            {if $oi.extra.discount|floatval}{include file="common/price.tpl" value=$oi.extra.discount}{else}-{/if}</td>
                    {/if}
                    {if $order_info.taxes && $settings.Checkout.tax_calculation != "subtotal"}
                        <td class="nowrap" data-th="{__("tax")}">
                            {if $oi.tax_value|floatval}{include file="common/price.tpl" value=$oi.tax_value}{else}-{/if}</td>
                    {/if}
                    <td class="right" data-th="{__("subtotal")}"><span>{if $oi.extra.exclude_from_calculate}{__("free")}{else}{include file="common/price.tpl" value=$oi.display_subtotal}{/if}</span></td>
                </tr>
            {/if}
            {/hook}
        {/foreach}

        {/if}
        {hook name="orders:extra_list"}
        {/hook}
    </table>
</div>