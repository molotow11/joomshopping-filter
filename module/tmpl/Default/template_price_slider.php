<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<script type="text/javascript">	
	<?php 
		$from = JFactory::getApplication()->input->get("price-from", 0);
		$to = JFactory::getApplication()->input->get("price-to", 0);

		if($from != 0 && $to != 0)
			$value = $from . " - " . $to;
		if($from == 0 && $to != 0)
			$value = $filter->slider_from . " - " . $to;
		if($from == 0 && $to == 0)
			$value = $filter->slider_from . " - " . $filter->slider_to;
	?>
	
	jQuery(document).ready(function() {
	
		jQuery("#slider_price<?php echo $module->id; ?>")[0].slide = null;
		jQuery("#slider_price<?php echo $module->id; ?>").slider({
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
				jQuery( "#slider_amount<?php echo $module->id; ?>" ).val(ui.values[0] + " - " + ui.values[1]);
				jQuery("input#slider_price<?php echo $module->id; ?>_val_from").val( ui.values[ 0 ] );
				jQuery("input#slider_price<?php echo $module->id; ?>_val_to").val( ui.values[ 1 ] );
			}
		});
		jQuery("#slider_amount<?php echo $module->id; ?>").val("<?php echo $value; ?>");
		
		jQuery("#slider_amount<?php echo $module->id; ?>").keyup(function() {
			var min = parseFloat(jQuery(this).val().replace(/\s|\.|,/g, "").split("-")[0]);
			var max = parseFloat(jQuery(this).val().replace(/\s|\.|,/g, "").split("-")[1]);
			jQuery("#slider_price<?php echo $module->id; ?>").slider("option", "values", [min, max]);
			jQuery("input#slider_price<?php echo $module->id; ?>_val_from").val(min);
			jQuery("input#slider_price<?php echo $module->id; ?>_val_to").val(max);
			
			<?php if($auto_counter) : ?>
			get_count<?php echo $module->id; ?>();
			<?php endif; ?>
			<?php if($auto_submit) : ?>
			submit_form_<?php echo $module->id; ?>();
			<?php endif; ?>
		});
	});
	</script>

	<div class="filter-field-price-slider">
		<h3>
			<?php echo JText::_('MOD_JSHOP_EFILTER_FIELDS_PRODUCT_PRICE_TITLE'); ?>
		</h3>

		<div class="slider_price<?php echo $module->id; ?>_wrapper slider_wrapper">

			<input type="text" id="slider_amount<?php echo $module->id; ?>" class="filter-slider-amount" />

			<div id="slider_price<?php echo $module->id; ?>"></div>
			
			<input id="slider_price<?php echo $module->id; ?>_val_from" class="slider_val" type="hidden" name="price-from" value="<?php if($from != 0) echo $from; ?>">
			
			<input id="slider_price<?php echo $module->id; ?>_val_to" class="slider_val" type="hidden" name="price-to" value="<?php if($to != 0) echo $to; ?>">
		
		</div>
	</div>

