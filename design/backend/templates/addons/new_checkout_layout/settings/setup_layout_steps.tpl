{if $is_backed_up}
    <div class="well">
        {__("new_checkout_layout.check_layout_back_ups", [
            "[file_path]" => {$file_path}
        ])}
    </div>
{/if}
<p>
    {__("new_checkout_layout.setup_layout_instruction")}
</p>
