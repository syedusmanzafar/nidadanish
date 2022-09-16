{$row = db_get_row('SELECT is_parent_order, parent_order_id FROM ?:orders WHERE order_id = ?i ', $order_info.order_id)}

<li>{btn type="list" text=$print_order href="orders.print_invoice?order_id=`$order_info.order_id`" class="cm-new-window"}</li>
<li>{btn type="list" text=$print_pdf_order href="orders.print_invoice?order_id=`$order_info.order_id`&format=pdf"}</li>
{if $settings.Appearance.email_templates == 'new'}
<li>{btn type="list" text=__("edit_and_send_invoice") href="orders.modify_invoice?order_id=`$order_info.order_id`"}</li>
{/if}
<li>{btn type="list" text=__("print_packing_slip") href="orders.print_packing_slip?order_id=`$order_info.order_id`" class="cm-new-window"}</li>
<li>{btn type="list" text=__("print_pdf_packing_slip") href="orders.print_packing_slip?order_id=`$order_info.order_id`&format=pdf" class="cm-new-window"}</li>

{if $row.parent_order_id != 0}
      <li>{btn type="list" text=__("edit_order") href="order_management.edit?order_id=`$order_info.order_id`"}</li>
      <li>{btn type="list" text=__("copy") href="order_management.edit?order_id=`$order_info.order_id`&copy=1"}</li>
{/if}

{$smarty.capture.adv_tools nofilter}