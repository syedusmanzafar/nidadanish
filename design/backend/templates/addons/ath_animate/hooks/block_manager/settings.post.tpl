{include file="common/subheader.tpl" title=__("animation_for_block_settings") target="#ath_block_animation"}

<div id="ath_block_animation" class="collapse in ath_animate-settings clearfix">

	<div class="ath_animate-settings__items">
	
		<div class="control-group">
			<label class="control-label" for="block_{$html_id}_anim_effect">{__("anim_effect")}</label>
			<div class="controls">
				<select name="snapping_data[anim_effect]" id="block_{$html_id}_anim_effect" class="js--animations_{$block.block_id}">
					<option value="">{__("none")}</option>
					{foreach from=$anim_effects item=anim_group key=anim_group_name}
					<optgroup label="{$anim_group_name}">
						{foreach from=$anim_group item=anim_effect}                            
							<option value="{$anim_effect}" {if $block.anim_effect == $anim_effect}selected="selected"{/if}>{$anim_effect}</option>
						{/foreach}
					</optgroup>
					{/foreach}
				</select>
				
			
			</div>
			<a class="btn btn-primary js--triggerAnimation_{$block.block_id} ath_animate-settings__start-btn">{__("start_animation")}</a>
		</div>
		
		<div id="anim_settings_{$html_id}">
			<div class="control-group">
				<label class="control-label" for="block_{$html_id}_anim_duration">{__("anim_duration")} <i class="icon-question-sign cm-tooltip" title="{__("anim_duration_help")}"></i></label>
				<div class="controls">
				<input type="text" name="snapping_data[anim_duration]" id="block_{$html_id}_anim_duration" size="25" value="{$block.anim_duration}"/>
				</div>
			</div>
		
			<div class="control-group">
				<label class="control-label" for="block_{$html_id}_anim_delay">{__("anim_delay")} <i class="icon-question-sign cm-tooltip" title="{__("anim_delay_help")}"></i></label>
				<div class="controls">
				<input type="text" name="snapping_data[anim_delay]" id="block_{$html_id}_anim_delay" size="25" value="{$block.anim_delay}"/>
				</div>
			</div>
		
			<div class="control-group">
				<label class="control-label" for="block_{$html_id}_anim_offset">{__("anim_offset")} <i class="icon-question-sign cm-tooltip" title="{__("anim_offset_help")}"></i></label>
				<div class="controls">
				<input type="text" name="snapping_data[anim_offset]" id="block_{$html_id}_anim_offset" size="25" value="{$block.anim_offset}"/>
				</div>
			</div>
		
			<div class="control-group">
				<label class="control-label" for="block_{$html_id}_anim_iteration">{__("anim_iteration")} <i class="icon-question-sign cm-tooltip" title="{__("anim_iteration_help")}"></i></label>
				<div class="controls">
				<input type="text" name="snapping_data[anim_iteration]" id="block_{$html_id}_anim_iteration" size="25" value="{$block.anim_iteration}"/>
				</div>
			</div>
		</div>
	</div>

	
	<div class="ath_animate-settings__preview">
		<span id="animationSandbox_{$block.block_id}" style="display: block;">
			<span class="ath_animate-settings__preview__block"></span>
		</span>
	</div>

	<script type="text/javascript">

			function testAnim_{$block.block_id}(x) {
				$('#animationSandbox_{$block.block_id}').removeClass().addClass(x + ' animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
				  $(this).removeClass();
				});
			};
			
			
		    $('.js--triggerAnimation_{$block.block_id}').click(function(e){
			console.log('oy');
				e.preventDefault();
				var anim = $('.js--animations_{$block.block_id}').val();
				testAnim_{$block.block_id}(anim);
		    });
		
		    $('.js--animations_{$block.block_id}').change(function(){
				var anim = $(this).val();
				testAnim_{$block.block_id}(anim);
		    });

	</script>

</div>

