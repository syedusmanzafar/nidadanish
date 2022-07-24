<?php
use Tygh\Registry;

define('AREA', 'A');
define('ACCOUNT_TYPE', 'admin');

    error_reporting(-1);
    ini_set('display_errors', 'on');
    ini_set('display_startup_errors', true);

$path = dirname(__FILE__) . '/error_log';
ini_set('log_errors', 'On');
ini_set('error_log', $path);

$regexp_phone = !empty($_REQUEST['regexp_phone']) ? urldecode($_REQUEST['regexp_phone']) : '';
$regexp_email = !empty($_REQUEST['regexp_email']) ? urldecode($_REQUEST['regexp_email']) : '';
$limit = !empty($_REQUEST['limit']) ? urldecode($_REQUEST['limit']) : '50';
$delete = !empty($_REQUEST['delete']) ? urldecode($_REQUEST['delete']) : '0';
$page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? $page = (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

require(dirname(__FILE__) . '/init.php');

	$admin_path_to_user = Registry::get('config.current_location') . '/' . Registry::get('config.admin_index') . '?dispatch=profiles.update&user_id=';

$condition = "user_type = 'C'";
if(!empty($regexp_phone)) {
    $condition .= db_quote(' AND phone REGEXP ?s', $regexp_phone);
}
if(!empty($regexp_email)) {
    $condition .= db_quote(' AND email REGEXP ?s', $regexp_email);
}

$fields = array('user_id', 'firstname', 'lastname', 'email');
$fields_new = implode(', ', $fields);

$bad_users_list = db_get_array("SELECT SQL_CALC_FOUND_ROWS ?p FROM ?:users WHERE ?p LIMIT ?i, ?i", $fields_new, $condition, $offset, $limit);
if (empty($bad_users_list)) {
echo 'There are no users that match specified regex(-es)';
} else {
echo '<table border="1"><thead><tr>';

foreach ($fields as $field) {
    echo('<th>'.$field.'</th>');
}

echo '<th>Link to admin</th>';
if ($delete==1) {
    echo '<th>DELETED? Y/N</th>';
}
echo '</tr></thead>';
echo '<tbody>';

foreach ($bad_users_list as $key => $el) {
    echo '<tr>';

    foreach ($el as $key_n => $el_n) {
        echo '<td>'.$el_n.'</td>';
    }
    echo '<td><a target="_blank" href="' . $admin_path_to_user . $el['user_id'] . '">Edit user</a></td>';
if ($delete==1) {
    fn_delete_user($el['user_id']);
    echo '<td>DELETED</td>';
}
    echo '</tr>';

}

echo '</tbody></table>';
$total_found = db_get_found_rows();

$totalpages = ceil($total_found / $limit);

if ($page > $totalpages) {
   $page = $totalpages;
}
if ($page < 1) {
   $page = 1;
}

/******  build the pagination links ******/
// range of num links to show
$range = 3;

//?page=
//?regexp_phone=$regexp_phone&regexp_email=$regexp_email&limit=$limit&page

// if not on page 1, don't show back links
if ($page > 1) {
   // show << link to go back to page 1
   echo " <a href='{$_SERVER['PHP_SELF']}?regexp_phone=$regexp_phone&regexp_email=$regexp_email&limit=$limit&page=1'><<</a> ";
   // get previous page num
   $prevpage = $page - 1;
   // show < link to go back to 1 page
   echo " <a href='{$_SERVER['PHP_SELF']}?regexp_phone=$regexp_phone&regexp_email=$regexp_email&limit=$limit&page=$prevpage'><</a> ";
} // end if 

// loop to show links to range of pages around current page
for ($x = ($page - $range); $x < (($page + $range) + 1); $x++) {
   // if it's a valid page number...
   if (($x > 0) && ($x <= $totalpages)) {
      // if we're on current page...
      if ($x == $page) {
         // 'highlight' it but don't make a link
         echo " [<b>$x</b>] ";
      // if not current page...
      } else {
         // make it a link
         echo " <a href='{$_SERVER['PHP_SELF']}?regexp_phone=$regexp_phone&regexp_email=$regexp_email&limit=$limit&page=$x'>$x</a> ";
      } // end else
   } // end if 
} // end for
                 
// if not on last page, show forward and last page links        
if ($page != $totalpages) {
   // get next page
   $nextpage = $page + 1;
    // echo forward link for next page 
   echo " <a href='{$_SERVER['PHP_SELF']}?regexp_phone=$regexp_phone&regexp_email=$regexp_email&limit=$limit&page=$nextpage'>></a> ";
   // echo forward link for lastpage
   echo " <a href='{$_SERVER['PHP_SELF']}?regexp_phone=$regexp_phone&regexp_email=$regexp_email&limit=$limit&page=$totalpages'>>></a> ";
} // end if


echo '<p>Total: '.$total_found.'</p>';

}



?>

<form  method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" >

<p>Regexp phone: <input type="checkbox" id="phone_checkbox" <?php if(!empty($regexp_phone)) echo("checked"); ?> ></input>
<input type="text" id="phone" name="regexp_phone" <?php if(!empty($regexp_phone)) {echo('value="'.$regexp_phone.'"');} else {echo("disabled");} ?> ></input></p>

<p>Regexp email: <input type="checkbox" id="email_checkbox" <?php if(!empty($regexp_email)) echo("checked"); ?> ></input>
<input type="text" id="email" name="regexp_email" <?php if(!empty($regexp_email)) {echo('value="'.$regexp_email.'"');} else {echo("disabled");} ?> ></input></p>

<p>Limit:
<input type="text" name="limit" value="<?php echo($limit); ?>"></input></p>

<p>DELETE? <input type="checkbox" id="delete" name="delete" value="1"></input></p>
<input type="submit" value="Update list with new RegExp">
</form>

<p><strong>Warning!</strong></p>
<p>If <b>DELETE?</b> is checked, your next search will delete found users (still outputting it's data before reloading page) keeping LIMIT setting in mind.<p>
<p>If you will reload page without modifying any parameters - it will perform new DELETE for new set of users (since old were removed).<p>
<p>Please do not tick <b>DELETE?</b> with modified arguments, otherwise you may face error or unexpected behaviour.<p>
<p>It's highly recommended to create database backup before applying any changes or running DELETE query.</p>
<p>User removal is handled by <b>fn_delete_user()</b> CS-Cart function that wipes all data of specific user.</p>
<p><b>DELETE</b> argument is not handled during page switching to avoid unexpected deletions.</p>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script type="text/javascript">
$(function() {
  $("#email_checkbox").click(enable_cb);
  $("#phone_checkbox").click(enable_cb2);
});

function enable_cb() {
  if (this.checked) {
    $("input#email").removeAttr("disabled");
  } else {
    $("input#email").attr("disabled", true);
  }
}

function enable_cb2() {
  if (this.checked) {
    $("input#phone").removeAttr("disabled");
  } else {
    $("input#phone").attr("disabled", true);
  }
}
</script>

<?php
echo('<br>');
echo('<p>Current SQL query: <b>' . db_quote("SELECT ?p FROM ?:users WHERE ?p LIMIT ?i, ?i", $fields_new, $condition, $offset, $limit)) . '</b></p>';
echo('<p>Current arguments:</p>');
fn_print_r($_GET);
echo '</body>';
