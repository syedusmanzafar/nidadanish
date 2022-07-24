<p>{__("`$lp`.professional_info")}</p>
<pre style=" color:#d14">
if ([opt_x] < 12){ 
   $price = [price] * 2;	
}elseif([opt_x] < 20)
   $price = [price] * 2 + [opt_x];	
}else{
   $price = [price] * 2 + [opt_x] - [glb_x];
}
$price = round($price, 2);  //Price rounded to decimals
</pre>

<p>{__("`$lp`.after_info")}</p>