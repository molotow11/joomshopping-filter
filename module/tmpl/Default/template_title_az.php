<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
	
<script>
	jQuery(document).ready(function() {
		var title_az = jQuery("input[name=title_az]").val();
		
		jQuery("#ExtendedFilterContainer<?php echo $module->id; ?> a.title_az").each(function() {
			if(title_az == jQuery(this).text()) {
				jQuery(this).addClass("active");
			}
		});
	
		jQuery("#ExtendedFilterContainer<?php echo $module->id; ?> a.title_az").click(function() {
			if(jQuery(this).hasClass("active") == 0) {
				jQuery("#ExtendedFilterContainer<?php echo $module->id; ?> a.title_az").removeClass("active");
				jQuery(this).addClass("active");
				jQuery("input[name=title_az]").val(jQuery(this).html());
			}
			else {
				jQuery(this).removeClass("active");
				jQuery("input[name=title_az]").val("");
			}
			<?php if($auto_submit) : ?>
			submit_form_<?php echo $module->id; ?>();
			<?php endif; ?>
			return false;
		});
	});
</script>

	<div class="filter-field-title-az">
	
		<h3>
			<?php echo JText::_('MOD_JSHOP_EFILTER_FIELDS_PRODUCT_TITLE_AZ_TITLE'); ?>
		</h3>
		
		<?php foreach(range('a', 'z') as $letter) : ?>
			<a class="title_az" href="#"><?php echo $letter; ?></a>
		<?php endforeach; ?>
		
		<input name="title_az" type="hidden" class="inputbox"<?php if (JFactory::getApplication()->input->get('title_az')) echo ' value="'.JFactory::getApplication()->input->get('title_az').'"'; ?> />
	</div>

