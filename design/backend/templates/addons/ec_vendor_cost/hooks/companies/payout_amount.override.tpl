{if $runtime.company_id}
    {if $payout.payout_type = "VendorPayoutTypes::PAYOUT"|enum && $payout.payout_amount < 0}
        <small class="muted">
            {include file="common/price.tpl" value=$payout.display_amount}
        </small>
    {else}
        {include file="common/price.tpl" value=$payout.display_amount}
    {/if}
{/if}