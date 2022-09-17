{script src="js/addons/ath_animate/wow.min.js"}
<script>	
	anim = new WOW(
		{
			{if $addons.ath_animate.mobile != "Y"}mobile: false,{/if}
		}
	)
	anim.init();
	
	$(document).ajaxComplete(function(event,request, settings) {
		anim.sync();
	});

</script>