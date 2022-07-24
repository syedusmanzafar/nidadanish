
{if $addons.cp_webp.force_generate != "Y"}
    <script language="javascript">
        (function(_,$){
            $(document).ready(function(){
                $.ceAjax('request', fn_url('webp.generate'), {
                    hidden: true 
                });
            });
        })(Tygh,Tygh.$);
    </script>
{/if}