{if $product}
    <input type="hidden" name="call_data[rf_price]" value="{$product.price}" />
    <script type="text/javascript">
        if (typeof window['_RF_CRITEO_ACCOUNT_ID'] != 'undefined') {ldelim}
            window.criteo_q = window.criteo_q || [];
            window.criteo_q.push(
                {ldelim} event: "setAccount", account: _RF_CRITEO_ACCOUNT_ID {rdelim},
                {ldelim} event: "setSiteType", type: _RF_CRITEO_DEVICE_TYPE {rdelim},
                {ldelim} event: "setHashedEmail", email: _RF_CRITEO_HASHED_EMAIL {rdelim},
                {ldelim} event: "viewBasket", item: {strip}[
                        {ldelim}id: "{$product.product_id}",
                            price: "{$product.price}",
                            quantity: "{$product.amount}"{rdelim}
                    ]{/strip} {rdelim}
            );
        {rdelim}
    </script>
{/if}