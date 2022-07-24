{if $runtime.controller == "cp_live_search" && $runtime.mode == "styles_update"}
<script type="text/javascript">
    (function (_,$) {
        $.ceEvent('on', 'ce.colorpicker.show', function (context) {
            $(window).trigger('resize');
        });
    })(Tygh, Tygh.$);
</script>
{/if}