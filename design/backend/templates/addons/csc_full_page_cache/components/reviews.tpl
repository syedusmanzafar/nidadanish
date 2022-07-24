{assign var=url value="https://www.cs-commerce.com/?addon_reviews={$addon}"}
<div class="{$addon}_reviews">
    <div class="sidebar-row ">      	
        <ul>
            <li class="stars"><a href="{$url}" target="_blank"><i></i><i></i><i></i><i></i><i></i></a>
            </li>
             <li class="rate-us"><a href="{$url}" target="_blank">{__("`$prefix`.rate_us")}</a></li> 
             <li class="rate-us"><a href="https://www.cs-commerce.com/" target="_blank">{__("`$prefix`.other_addons")}</a></li>
              <li class="rate-us"><hr/></li>
             <li class="rate-us"><font size="4" style="vertical-align:bottom">&#9993;</font> <a style="color:#333" target="_blank" href="https://www.cs-commerce.com/index.php?dispatch=auther.helpdesk">{__("`$prefix`.feedback")}</a></li>                                 
        </ul>
    </div>
</div>
