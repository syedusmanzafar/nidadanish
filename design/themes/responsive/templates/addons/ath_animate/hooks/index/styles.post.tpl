{style src="addons/ath_animate/animate.min.css"}
{style src="addons/ath_animate/styles.less"}

{* IE11 fix *}
<style>
	@media all and (-ms-high-contrast:none) {
		*::-ms-backdrop, .ty-pagination-container .wow,
		*::-ms-backdrop, .ty-compact-list .wow,
		*::-ms-backdrop, .grid-list .wow,
		*::-ms-backdrop, .ty-grid-list__item .wow { 
			animation-delay: 0 !important;
		} /* IE11 */
	}
</style>
