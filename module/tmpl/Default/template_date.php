<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<script type="text/javascript">
	jQuery(document).ready(function () {
		jQuery("input.datepicker").datepicker({ dateFormat: 'yy-mm-dd' });
	});
</script>

	<div class="filter-field-date">
	
		<h3>
			<?php echo JText::_('MOD_JSHOP_EFILTER_FIELDS_PRODUCT_DATE_TITLE'); ?>
		</h3>
		
		<input style="width: 44%; max-width: 100px;" class="datepicker inputbox" name="date-from" type="text" <?php if (JFactory::getApplication()->input->get('date-from')) echo ' value="'.JFactory::getApplication()->input->get('date-from').'"'; ?> /> - 
		<input style="width: 44%; max-width: 100px;" class="datepicker inputbox" name="date-to" type="text" <?php if (JFactory::getApplication()->input->get('date-to')) echo ' value="'.JFactory::getApplication()->input->get('date-to').'"'; ?> />
	</div>

