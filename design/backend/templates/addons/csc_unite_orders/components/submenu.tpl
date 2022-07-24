{foreach from = $submenu item="ssmenu" key="head"}
<div class="sidebar-row" id="views">
  <h6>{__($head)}</h6>
 
      <ul class="nav nav-list saved-search">             
          {foreach from=$ssmenu item=smenu key='name'}                         
              <li {if $smenu.dispatch == $smarty.request.dispatch}class="active"{/if}>                   
                  <a class="cm-view-name" href="{$smenu.dispatch|fn_url}">{__($name)}</a>
              </li>
          {/foreach}          
      </ul>
</div>
{/foreach} 