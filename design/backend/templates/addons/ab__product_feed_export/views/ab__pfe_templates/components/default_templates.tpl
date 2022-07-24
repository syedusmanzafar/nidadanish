<script type="text/javascript" class="cm-ajax-force">
function get_default_template (name){
if (name.length > 0){
$.ceAjax('request', fn_url("ab__pfe_templates.get_default_template?name=" + name), {
method: 'get',
callback: function(data) {
$('textarea#elm_template').val(data.template);
}
});
}
}
</script>
<div class="sidebar-row">
<h6>{__("ab__pfe.template.default_templates")}</h6>
<div class="sidebar-field">
<label>{__("ab__pfe.template.default_templates_list")}</label>
<select name="default_template">
<option value="">---</option>
{if $default_templates}
{foreach from=$default_templates item="i" key="k"}
<option value="{$k}">{$i.name}</option>
{/foreach}
{/if}
</select>
</div>
{btn type="text" text=__("ab__pfe.template.get_def_template") class="btn" onclick="get_default_template($('select[name=default_template]').val())"}
</div><hr>
