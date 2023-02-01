<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<script type="text/javascript">
	
	function addCommas(nStr)
	{
		nStr += '';
		x = nStr.split('.');
		x1 = x[0];
		x2 = x.length > 1 ? '.' + x[1] : '';
		var rgx = /(\d+)(\d{3})/;
		while (rgx.test(x1)) {
			x1 = x1.replace(rgx, '$1' + '.' + '$2');
		}
		return x1 + x2;
	}
	
	<?php 
		$from = JFactory::getApplication()->input->get("char".$filter->id."-from", 0);
		$to = JFactory::getApplication()->input->get("char".$filter->id."-to", 0);

		if($from != 0 && $to != 0)
			$value = number_format($from, 0, '', '.'). " - " .number_format($to, 0, '', '.');
		if($from == 0 && $to != 0)
			$value = number_format($filter->slider_from, 0, '', '.') . " - ".number_format($to, 0, '', '.');
		if($from == 0 && $to == 0)
			$value = number_format($filter->slider_from, 0, '', '.') . " - " . number_format($filter->slider_to, 0, '', '.');
	?>
	
	jQuery(document).ready(function() {
	
		jQuery("#slider_char<?php echo $filter->id; ?>_<?php echo $module->id; ?>")[0].slide = null;
		jQuery("#slider_char<?php echo $filter->id; ?>_<?php echo $module->id; ?>").slider({
			range: true,
			min: <?php echo $filter->slider_from; ?>,
			max: <?php echo $filter->slider_to; ?>,
			step: 1,
			values: [ <?php if($from != 0) echo $from; else echo $filter->slider_from; ?>, <?php if($to != 0) echo $to; else echo $filter->slider_to; ?> ],
			stop: function(event, ui) {
				<?php if($auto_counter) : ?>
				jQuery(this).parents(".slider_wrapper").find("input.filter-slider-amount").trigger("change");
				<?php endif; ?>
				<?php if($auto_submit) : ?>
				submit_form_<?php echo $module->id; ?>();
				<?php endif; ?>
			},
			slide: function(event, ui) {
				jQuery( "#slider_amount_char<?php echo $filter->id; ?>_<?php echo $module->id; ?>" ).val( addCommas(ui.values[ 0 ]) + " - " + addCommas(ui.values[ 1 ]) );
				jQuery("input#slider_char<?php echo $filter->id; ?>_<?php echo $module->id; ?>_val_from").val( ui.values[ 0 ] );
				jQuery("input#slider_char<?php echo $filter->id; ?>_<?php echo $module->id; ?>_val_to").val( ui.values[ 1 ] );
			}
		});
		jQuery("#slider_amount_char<?php echo $filter->id; ?>_<?php echo $module->id; ?>").val("<?php echo $value; ?>");
		
		jQuery("#slider_amount_char<?php echo $filter->id; ?>_<?php echo $module->id; ?>").keyup(function() {
			var min = parseFloat(jQuery(this).val().replace(/\s|\.|,/g, "").split("-")[0]);
			if(isNaN(min)) {
				min = <?php echo $filter->slider_from; ?>;
			}
			var max = parseFloat(jQuery(this).val().replace(/\s|\.|,/g, "").split("-")[1]);
			if(isNaN(max)) {
				max = <?php echo $filter->slider_to; ?>;
			}			

			jQuery("#slider_char<?php echo $filter->id; ?>_<?php echo $module->id; ?>").slider("option", "values", [min, max]);
			jQuery("input#slider_char<?php echo $filter->id; ?>_<?php echo $module->id; ?>_val_from").val(min);
			jQuery("input#slider_char<?php echo $filter->id; ?>_<?php echo $module->id; ?>_val_to").val(max);
			
			<?php if($auto_counter) : ?>
			get_count<?php echo $module->id; ?>();
			<?php endif; ?>
			<?php if($auto_submit) : ?>
			submit_form_<?php echo $module->id; ?>();
			<?php endif; ?>
		});
	});
	</script>

	<div class="filter-field-char-slider">
		<h3>
			<?php echo $filter->title; ?>
		</h3>

		<div class="slider_char<?php echo $filter->id; ?>_<?php echo $module->id; ?>_wrapper slider_wrapper">

			<input type="text" id="slider_amount_char<?php echo $filter->id; ?>_<?php echo $module->id; ?>" class="filter-slider-amount" />

			<div id="slider_char<?php echo $filter->id; ?>_<?php echo $module->id; ?>"></div>
			
			<input id="slider_char<?php echo $filter->id; ?>_<?php echo $module->id; ?>_val_from" class="slider_val" type="hidden" name="char<?php echo $filter->id; ?>-from" value="<?php if($from != 0) echo $from; ?>">
			
			<input id="slider_char<?php echo $filter->id; ?>_<?php echo $module->id; ?>_val_to" class="slider_val" type="hidden" name="char<?php echo $filter->id; ?>-to" value="<?php if($to != 0) echo $to; ?>">
		
		</div>
	</div>

