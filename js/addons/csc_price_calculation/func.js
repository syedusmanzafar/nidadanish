$(document).on('click', '.csc_delete_plh', function(){
	$(this).attr('placeholder', '');
});

function fn_check_value(input) {
	$(input).val($(input).val().replace(',', '.'));
	$(input).val($(input).val().replace('..', '.'));
	var value = input.value; 
	var rep = /[^0-9.]/;
	if (rep.test(value)) { 
		value = value.replace(rep, ''); 
		input.value = value; 
	}
} 
function fn_calc_price(input, min_v, max_v, step, option_id, id, prefix) {
	
	var val = $(input).val();		
	if (val == '' || val<min_v) {		
		$(input).val('');
		$(input).attr('placeholder', val);
		return false;
	}
	if (val > max_v && max_v > 0) {
		$(input).val('');
		$(input).attr('placeholder', val);
		return false;
		//$(input).val(max_v);
	}
	var starting = val-min_v;		
	var df = starting % step;				
	if (df){
		var k = Math.ceil((val-min_v)/step);
	
		var r=Math.round(((k*step)+min_v)*100);
							
		$(input).val(r/100);
	}
	
	fn_change_options(prefix + id, id, option_id);
	return false;
}
	
function fn_increase_csc_price_calc_numeric(data, option_id, prefix, id){	
	var number = data.value;	
	if (number=="C"){
		$("#option_"+prefix+"_"+option_id+"_input_block").show();			
	}else{		
		var value = parseFloat(number);
		$("#option_"+prefix+"_"+option_id+"_input_block").hide();
		$("#option_"+prefix+"_"+option_id).val(value);	
		fn_change_options(prefix, id, option_id);	
	}
}
$(document).on("change", ".ty-value-changer__input", function () {
	if (this.id.indexOf("amount_")>-1){
		id = this.id.replace(/amount_/g,'');
	}else if (this.id.indexOf("qty_count_")>-1){
		id = this.id.replace(/qty_count_/g,'');		
	}
   fn_change_options(id, id, '');
});
$(document).on("change", ".ty-qty select", function () {
	id = this.id.replace(/qty_count_/g,'');	
	fn_change_options(id, id, '');
});
$(document).on("click", ".ty-value-changer__increase", function () {
	fid = this.parentNode.parentNode.id;
	if (fid.indexOf("qty_")>-1){
		id = fid.replace(/qty_/g,'');
	}else if (fid.indexOf("quantity_update_")>-1){
		id = fid.replace(/quantity_update_/g,'');		
	}
	setTimeout("fn_change_options(id, id, '')", 50);
});
$(document).on("click", ".ty-value-changer__decrease", function () {
	fid = this.parentNode.parentNode.id;
	if (fid.indexOf("qty_")>-1){
		id = fid.replace(/qty_/g,'');
	}else if (fid.indexOf("quantity_update_")>-1){
		id = fid.replace(/quantity_update_/g,'');		
	}
	setTimeout("fn_change_options(id, id, '')", 50);
});
