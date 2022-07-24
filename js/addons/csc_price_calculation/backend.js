function fn_csc_show_price_tab(value, id){	
	if (value=="N"){
		$("#tab_price_addon_variants_"+id).show();
		$("#tab_price_variants_"+id).show();				
	}else{
		$("#tab_price_addon_variants_"+id).hide();
		$("#tab_price_variants_"+id).hide();		
	}
	
	if (value=="X"){
		$("#price_calc_x_block_"+id).show();					
	}else{
		$("#price_calc_x_block_"+id).hide();		
	}
	
}

function fn_add_value_to_formula(value){		
	old = $("#product_data_formula").val();
	new_value = old+value;
	$("#product_data_formula").val(new_value);
}

function fn_check_values_on_csc_defaults(input){
	var value = input.value; 
	var rep = /[^0-9.a-z_\[\]]/;
	if (rep.test(value)) { 
		value = value.replace(rep, ''); 
		input.value = value; 
	}
}

function fn_csc_check_formula(input){
  var value = input.value; 
  var rep = /[^0-9%*\/()+-.\/a-z_\[\]]/;
  if (rep.test(value)) { 
	  value = value.replace(rep, ''); 
	  input.value = value; 
  }
}